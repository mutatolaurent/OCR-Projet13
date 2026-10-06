<?php

namespace App\Controller;

use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Ce contrôleur est responsable de la création des commandes à partir du panier de l'utilisateur.
 * Il utilise le service OrderService pour encapsuler la logique métier de création de commande.
 */
#[IsGranted('ROLE_USER')]
final class OrderController extends AbstractController
{
    /**
     * Crée une commande à partir du panier de l'utilisateur
     * et redirige vers la page "mon compte" sur laquelle sont affichées les commandes
     *
     * @param OrderService $orderService Le service pour gérer la logique de création de commande
     * @return Response La réponse HTTP après la création de la commande
     */
    #[Route('/order/create', name: 'app_order_create')]
    public function create(OrderService $orderService): Response
    {
        // Récupérer l'utilisateur actuellement connecté
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        try {
            // Appel du service pour créer la commande à partir du panier de l'utilisateur
            $order = $orderService->createOrderFromBasket($user);
        } catch (\LogicException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_main');
        }

        // Ajouter un message flash pour informer l'utilisateur que la commande a été créée avec succès
        $this->addFlash('success', 'Votre commande CMD-' . $order->getId() . ' a été créée avec succès !');

        // Rediriger l'utilisateur vers sa page de compte ou une page de confirmation
        return $this->redirectToRoute('app_account');
    }
}
