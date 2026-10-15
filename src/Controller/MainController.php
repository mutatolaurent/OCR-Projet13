<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;

/**
 * Ce contrôleur est responsable de la page principale de l'application.
 * Il affiche la liste des produits disponibles sur la page d'accueil.
 */
final class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(ProductRepository $repository): Response
    {

        $products = $repository->findAll();

        return $this->render('main/index.html.twig', [
            'products' => $products,
        ]);
    }
}
