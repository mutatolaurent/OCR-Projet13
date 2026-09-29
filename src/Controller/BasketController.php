<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Basket;
use App\Service\BasketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
class BasketController extends AbstractController
{
    #[Route('/basket/add/{id<\d+>}', name: 'basket_add', methods: ['POST'])]
    public function addItem(Product $product, Request $request, BasketService $basketService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Récupérer la quantité envoyée en POST (par défaut 1)
        $quantity = (int) $request->request->get('quantite', 1);

        // Appel du service pour gérer toute la logique métier du panier
        $basketService->addOrUpdateItem($user, $product, $quantity);

        $this->addFlash('success', 'Votre panier a été mis à jour.');

        return $this->redirectToRoute('app_basket');
    }

    #[Route('/basket', name: 'app_basket', methods: ['GET'])]
    public function show(BasketService $basketService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Récupération des données formatées directement via le service
        $basketData = $basketService->getBasketDetails($user);

        return $this->render('basket/basket.html.twig', [
            'basketItems' => $basketData['basketItems'],
            'totalGeneral' => $basketData['totalGeneral'],
        ]);

    }
}
