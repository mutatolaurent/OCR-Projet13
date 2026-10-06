<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\BasketService;

/**
 * Ce contrôleur est responsable de la gestion du compte utilisateur.
 * Il permet à l'utilisateur de consulter ses commandes, de gérer son accès API et de supprimer son compte.
 */
final class AccountController extends AbstractController
{
    /**
     * Affiche la page du compte utilisateur avec la liste de ses commandes.
     *
     * @return Response La réponse HTTP contenant la page du compte utilisateur
     */
    #[Route('/account', name: 'app_account')]
    public function show(): Response
    {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Récupérer les commandes de l'utilisateur déjà triées par date de création décroissante
        $orders = $user->getSalesOrders()->toArray(); // Convertir la collection en tableau pour l'utiliser dans le rendu

        return $this->render('account/account.html.twig', [
            'orders' => $orders,
        ]);

    }

    /**
     * Permet à l'utilisateur de basculer l'accès API de son compte.
     *
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour persister les changements
     * @return Response La réponse HTTP après la mise à jour de l'accès API
     */
    #[Route('/account/toggle-api-access', name: 'app_account_toggle_api_access')]
    public function toggleApiAccess(EntityManagerInterface $entityManager): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        // Toggle the accesAPI attribute
        // If it's null or false, set to true. If true, set to false.
        $user->setAccesAPI(!$user->isAccesAPI());

        $entityManager->persist($user);
        $entityManager->flush();

        // Add a flash message
        $status = $user->isAccesAPI() ? 'activé' : 'désactivé';
        $this->addFlash('success', 'Votre accès API a été ' . $status . ' avec succès.');

        // Redirect back to the account page
        return $this->redirectToRoute('app_account');
    }

    /**
     * Anonymise le compte de l'utilisateur et supprime son panier associé.
     *
     * @param EntityManagerInterface $entityManager Le gestionnaire d'entités pour persister les changements
     * @param BasketService $basketService Le service pour gérer la logique du panier
     * @return Response La réponse HTTP après l'anonymisation du compte
     */
    #[Route('/account/delete', name: 'app_account_delete', methods: ['POST'])]
    public function deleteAccount(EntityManagerInterface $entityManager, BasketService $basketService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour supprimer votre compte.');
            return $this->redirectToRoute('app_login');
        }

        // 1. Supprimer le panier si l'utilisateur en a un
        $basket = $user->getBasket();
        if ($basket) {
            $entityManager->remove($basket);
        }

        // 2. Anonymiser le compte du User
        $user->setEmail('anonymous-' . $user->getId() . '@example.com');
        $user->setFirstName('Anonyme');
        $user->setLastName('Anonyme');
        $user->setPassword(bin2hex(random_bytes(32)));
        $user->setRoles([]);
        $user->setAccesAPI(false);

        $entityManager->flush();

        // 3. Générer un message flash
        $this->addFlash('success', 'Votre compte a bien été supprimé, vous êtes redirigé vers la page d\'accueil.');

        // 4. Déconnecter l'utilisateur et le rediriger vers la page d'accueil
        return $this->redirectToRoute('app_logout');
    }

}
