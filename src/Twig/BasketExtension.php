<?php

namespace App\Twig;

use App\Service\BasketService;
use Symfony\Bundle\SecurityBundle\Security;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class BasketExtension extends AbstractExtension
{
    public function __construct(
        private BasketService $basketService,
        private Security $security
    ) {
    }

    public function getFunctions(): array
    {
        return [
            // Déclare une fonction Twig nommée 'basket_count'
            new TwigFunction('basket_count', [$this, 'getBasketCount']),
        ];
    }

    public function getBasketCount(): int
    {
        /** @var \App\Entity\User|null $user */
        $user = $this->security->getUser();

        // Si l'utilisateur n'est pas connecté, le panier est vide (0)
        if (!$user) {
            return 0;
        }

        return $this->basketService->getTotalQuantity($user);
    }
}
