<?php

namespace App\Controller;

use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\SalesOrderRepository;

/**
 * Ce contrôleur est responsable de la gestion des commandes (Sales Orders) dans l'application.
 * Il fournit des fonctionnalités pour créer une commande à partir du panier de l'utilisateur
 * et pour récupérer la liste des commandes de l'utilisateur connecté via une API.
 */
#[IsGranted('ROLE_USER')]
final class SalesOrderController extends AbstractController
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

    /**
     * Récupère la liste des commandes de l'utilisateur connecté via l'API.
     *
     * @param SalesOrderRepository $orderRepository Le dépôt pour récupérer les commandes.
     * @param SerializerInterface $serializer Le sérialiseur pour convertir les objets en JSON.
     * @return JsonResponse La réponse JSON contenant la liste des commandes.
     */
    #[Route('/api/orders', name: 'api_order_list', methods: ['GET'])]
    public function getOrderList(SalesOrderRepository $orderRepository, SerializerInterface$serializer): JsonResponse
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

        // Récupération des commandes de l'utilisateur connecté
        $orderList = $orderRepository->findByUserWithProducts($user);

        // Sérialisation de la liste des commandes en JSON avec le groupe dédié
        $jsonOrderList = $serializer->serialize($orderList, 'json', ['groups' => 'order:read']);

        // Retourne la liste des commandes sous forme de réponse JSON (le 4ème paramètre `true` indique que c'est déjà du JSON brut)
        return new JsonResponse($jsonOrderList, JsonResponse::HTTP_OK, [], true);
    }
}
