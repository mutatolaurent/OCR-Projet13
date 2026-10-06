<?php

namespace App\Controller;

use App\Entity\BasketProduct;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

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
}
