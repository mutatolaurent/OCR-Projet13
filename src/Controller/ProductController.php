<?php

namespace App\Controller;

use App\Entity\BasketProduct;
use App\Entity\Product;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;
use Symfony\Component\HttpFoundation\RequestStack;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Ce contrôleur est responsable de la gestion des produits.
 * Il permet d'afficher les fiches produits en utilisant soit l'ID, soit le slug du produit.
 */
final class ProductController extends AbstractController
{
    public function __construct(
        private string $apiCacheTag,
        private int $apiCacheTtl
    ) {
    }

    /**
     * Affiche la fiche produit en utilisant l'ID du produit.
     *
     * @param Product $product L'entité Product correspondant à l'ID fourni
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour récupérer les informations du panier
     * @return Response La réponse HTTP contenant la fiche produit
     */
    #[Route('/product/id/{id<\d+>}', name: 'app_product_by_id')]
    public function showById(Product $product, EntityManagerInterface $entityManager): Response
    {
        return $this->renderProductPage($product, $entityManager);
    }

    /**
     * Affiche la fiche produit en utilisant le slug du produit.
     *
     * @param Product $product L'entité Product correspondant au slug fourni
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour récupérer les informations du panier
     * @return Response La réponse HTTP contenant la fiche produit
     */
    #[Route('/product/{slug}', name: 'app_product')]
    public function showBySlug(
        #[MapEntity(mapping: ['slug' => 'slug'])] Product $product,
        EntityManagerInterface $entityManager
    ): Response {
        return $this->renderProductPage($product, $entityManager);
    }

    /**
     * Rendu de la page produit avec les informations du panier si l'utilisateur est connecté.
     *
     * @param Product $product L'entité Product à afficher
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour récupérer les informations du panier
     * @return Response La réponse HTTP contenant la fiche produit
     */
    private function renderProductPage(Product $product, EntityManagerInterface $entityManager): Response
    {
        $basketProduct = null;

        /** @var \App\Entity\User|null $user */
        $user = $this->getUser();

        // Si l'utilisateur est connecté et possède un panier, on cherche si le produit y est déjà
        if ($user && $user->getBasket()) {
            $basketProduct = $entityManager->getRepository(BasketProduct::class)->findOneBy([
                'basket' => $user->getBasket(),
                'product' => $product,
            ]);
        }

        return $this->render('product/product.html.twig', [
            'product' => $product,
            'basketProduct' => $basketProduct, // On passe l'objet ou null à Twig
        ]);
    }

    /**
     * Récupère la liste des produits via l'API.
     *
     * @param ProductRepository $productRepository Le repository des produits
     * @param SerializerInterface $serializer Le sérialiseur pour convertir les données en JSON
     * @param RequestStack $requestStack La pile de requêtes pour obtenir la requête courante
     * @return JsonResponse La réponse JSON contenant la liste des produits
     */
    #[Route('/api/products', name: 'api_product_list', methods: ['GET'])]
    public function getProductList(
        ProductRepository $productRepository,
        SerializerInterface $serializer,
        RequestStack $requestStack,
        #[Autowire(service: 'monolog.logger.api')] LoggerInterface $apiLogger,
        TagAwareCacheInterface $cachePool
    ): JsonResponse {

        /** @var User|null $user */
        $user = $this->getUser();

        // Récupération de l'URL de base pour construire les URLs complètes des images
        $request = $requestStack->getCurrentRequest();

        // Vérification de l'accès à l'API
        if (!$user || !$user->isAccesAPI()) {

            $statusCode = JsonResponse::HTTP_FORBIDDEN; // 403
            $message = 'Veuillez activer votre accès API dans votre espace client.';

            $apiLogger->error(sprintf('[API_AUTH_FORBIDDEN] Erreur %d : %s', $statusCode, $message), [
                'source' => 'API_AUTH_FORBIDDEN',
                'status_code' => $statusCode,
                'url' => $request->getUri(),
                'method' => $request->getMethod(),
            ]);

            return new JsonResponse([
                'status' => $statusCode,
                'message' =>  $message,
            ], JsonResponse::HTTP_FORBIDDEN); // Statut 403 Forbidden
        }

        // Générer une clé de cache unique pour la liste des produits
        $idCache = "getAllProducts";

        // Vérifier si la réponse est déjà en cache
        $isHit = true;

        // Récupération de la liste des produits depuis le cache ou génération si non présent
        $jsonProductList = $cachePool->get($idCache, function (ItemInterface $item) use ($productRepository, $serializer, $request, &$isHit, $apiLogger) {

            $start = microtime(true);

            // Indique que la réponse n'était pas en cache
            $isHit = false;

            // Tag pour l'invalidation du cache
            $item->tag($this->apiCacheTag);

            // Définir la durée de vie du cache
            $item->expiresAfter($this->apiCacheTtl);

            // Récupération du schéma et de l'hôte (ex: http://localhost ou https://example.com)
            $schemeAndHost = $request->getSchemeAndHttpHost();

            // Récupération du chemin de l'image depuis les paramètres
            $imagePathPrefix = $this->getParameter('image_paths_public')['product'];

            // Récupération de la liste des produits depuis le repository
            $productList = $productRepository->findAll();

            foreach ($productList as $product) {

                // Construction de l'URL complète de l'image
                $relativePathToAsset = $imagePathPrefix . $product->getPicture();
                $fullImageUrl = $schemeAndHost . $relativePathToAsset; // Construction de l'URL absolue complète

                // Mettre à jour l'attribut 'picture' de l'entité Product avec l'URL complète
                $product->setPicture($fullImageUrl);
            }

            $duration = (microtime(true) - $start) * 1000;
            $apiLogger->error("Temps d'exécution : {$duration}ms");

            // Sérialisation de la liste des produits en JSON
            return $serializer->serialize($productList, 'json', ['groups' => 'product:read']);

        });

        // Retourne la liste des produits sous forme de réponse JSON
        $response = new JsonResponse($jsonProductList, JsonResponse::HTTP_OK, [], true);

        // Ajoute un en-tête personnalisé pour indiquer si la réponse provient du cache ou non
        $response->headers->set('X-Cache', $isHit ? 'HIT' : 'MISS');

        // Directives Cache-Control pour LE CLIENT
        $response->setCache([
            'max_age'       => $this->apiCacheTtl, // Le client peut cacher la réponse pendant $apiCacheTtl secondes
            's_maxage'      => $this->apiCacheTtl, // Les proxys/CDN peuvent aussi cacher la réponse pendant $apiCacheTtl secondes
            'public'        => true, // Permet la mise en cache par tout le monde
        ]);

        return $response;

    }
}
