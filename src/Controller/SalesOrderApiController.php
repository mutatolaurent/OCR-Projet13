<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use App\Repository\SalesOrderRepository;

/**
 * Ce contrôleur permet de récupérer la liste des commandes de l'utilisateur connecté via une API.
 */
#[IsGranted('ROLE_USER')]
final class SalesOrderApiController extends AbstractController
{
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
