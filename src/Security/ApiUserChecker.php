<?php

// src/Security/ApiUserChecker.php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ApiUserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // Vérification de votre champ booléen accesAPI
        if (!$user->isAccesAPI()) {
            // Cette exception renverra un message propre à l'utilisateur
            throw new CustomUserMessageAccountStatusException("Votre compte n'est pas autorisé à utiliser l'API.");
        }
    }

    public function checkPostAuth(UserInterface $user): void
    {
        // Rien de particulier ici, mais la méthode doit être présente pour respecter l'interface
        if (!$user instanceof User) {
            return;
        }
    }
}
