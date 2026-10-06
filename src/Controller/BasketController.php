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
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[IsGranted('ROLE_USER')]
class BasketController extends AbstractController
{
    /**
     * Ajoute un produit au panier ou met à jour sa quantité.
     *
     * @param Product $product Le produit à ajouter ou mettre à jour
     * @param Request $request La requête HTTP contenant la quantité
     * @param BasketService $basketService Le service pour gérer la logique du panier
     * @return Response La réponse HTTP après l'ajout ou la mise à jour
     */
    #[Route('/basket/add/{id<\d+>}', name: 'app_basket_add', methods: ['POST'])]
    public function addItem(
        Product $product,
        Request $request,
        BasketService $basketService,
        #[Autowire('%basket_max_quantity%')] int $maxQuantity
    ): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Récupérer la quantité envoyée en POST (par défaut 1)
        $quantity = (int) $request->request->get('quantite', 1);

        // Contrôle de la quantité
        if ($quantity < 0) {
            $this->addFlash('error', 'La quantité ne peut pas être négative.');
            return $this->redirectToRoute('app_product', ['slug' => $product->getSlug()]);
        }

        if ($quantity > $maxQuantity) {
            $this->addFlash('error', 'La quantité maximale autorisée est de ' . $maxQuantity . ' articles.');
            return $this->redirectToRoute('app_product', ['slug' => $product->getSlug()]);
        }

        // Appel du service pour gérer toute la logique métier du panier
        $basketService->addOrUpdateItem($user, $product, $quantity);

        $this->addFlash('success', 'Votre panier a été mis à jour.');

        return $this->redirectToRoute('app_basket');
    }

    /**
     * Affiche le contenu du panier.
     *
     * @param BasketService $basketService Le service pour gérer la logique du panier
     * @return Response La réponse HTTP contenant le rendu du panier
     */
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

    /**
     * Vide le panier de l'utilisateur.
     *
     * @param BasketService $basketService Le service pour gérer la logique du panier
     * @return Response La réponse HTTP après le vidage du panier
     */
    #[Route('/basket/clear', name: 'app_basket_clear', methods: ['POST'])]
    public function clear(BasketService $basketService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Appel du service pour supprimer le panier
        $basketService->clearBasket($user);

        $this->addFlash('success', 'Votre panier a été entièrement vidé.');

        return $this->redirectToRoute('app_basket');
    }
}
