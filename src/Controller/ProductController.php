<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use App\Entity\Product;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;

final class ProductController extends AbstractController
{
    #[Route('/product/id/{id<\d+>}', name: 'app_product_by_id')]
    public function showById(Product $product): Response
    {
        return $this->render('product/product.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/product/{slug}', name: 'app_product')]
    public function showBySlug(
        #[MapEntity(mapping: ['slug' => 'slug'])] Product $product
    ): Response {
        return $this->render('product/product.html.twig', [
            'product' => $product,
        ]);
    }
}
