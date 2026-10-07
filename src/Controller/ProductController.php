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

/**
 * Ce contrôleur est responsable de la gestion des produits.
 * Il permet d'afficher les fiches produits en utilisant soit l'ID, soit le slug du produit.
 */
final class ProductController extends AbstractController
{
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
    public function getProductList(ProductRepository $productRepository, SerializerInterface $serializer, RequestStack $requestStack): JsonResponse
    {

        /** @var User|null $user */
        $user = $this->getUser();

        // Vérification de l'accès à l'API
        if (!$user || !$user->isAccesAPI()) {
            return new JsonResponse([
                'error' => 'Accès API non autorisé.',
                'message' => 'Veuillez activer votre accès API dans votre espace client.'
            ], JsonResponse::HTTP_FORBIDDEN); // Statut 403 Forbidden
        }

        // Récupération de l'URL de base pour construire les URLs complètes des images
        $request = $requestStack->getCurrentRequest();

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

        // Sérialisation de la liste des produits en JSON
        $jsonProductList = $serializer->serialize($productList, 'json', ['groups' => 'product:read']);

        // Retourne la liste des produits sous forme de réponse JSON
        return new JsonResponse($jsonProductList, JsonResponse::HTTP_OK, [], true);

    }
}
