<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Ce contrôleur est responsable de la gestion de la sécurité, notamment de la connexion et de la déconnexion des utilisateurs.
 * Il fournit des fonctionnalités pour afficher le formulaire de connexion et gérer les erreurs de connexion.
 */
class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(
        AuthenticationUtils $authenticationUtils,
        #[Autowire(service: 'monolog.logger.web')] LoggerInterface $webLogger
    ): Response {
        // Récupère l'erreur de connexion précédente, s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier nom d'utilisateur saisi par l'utilisateur lors de la tentative de connexion
        $lastUsername = $authenticationUtils->getLastUsername();

        // Enregistre un avertissement dans le journal si une erreur de connexion s'est produite
        if ($error) {
            $webLogger->warning('Échec de connexion', [
                'username' => $lastUsername,
                'message' => $error->getMessageKey(),
                'code' => $error->getCode(),
            ]);
        }

        // Rendu de la page de connexion avec le dernier nom d'utilisateur et l'erreur éventuelle
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
