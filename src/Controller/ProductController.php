<?php

namespace App\Controller;

use App\Entity\BasketProduct;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

final class ProductController extends AbstractController
{
    #[Route('/product/id/{id<\d+>}', name: 'app_product_by_id')]
    public function showById(Product $product, EntityManagerInterface $entityManager): Response
    {
        return $this->renderProductPage($product, $entityManager);
    }

    #[Route('/product/{slug}', name: 'app_product')]
    public function showBySlug(
        #[MapEntity(mapping: ['slug' => 'slug'])] Product $product,
        EntityManagerInterface $entityManager
    ): Response {
        return $this->renderProductPage($product, $entityManager);
    }

    /**
     * Méthode privée pour factoriser la logique d'affichage de la fiche produit
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
