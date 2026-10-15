<?php

namespace App\Service;

use App\Entity\Basket;
use App\Entity\BasketProduct;
use App\Entity\Product;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Service pour gérer la logique métier du panier (Basket) de l'utilisateur.
 * Il fournit des méthodes pour ajouter, mettre à jour, supprimer des produits du panier,
 * ainsi que pour récupérer les détails du panier et le nombre total d'articles.
 */
class BasketService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Ajoute, met à jour ou supprime un produit du panier d'un utilisateur.
     * Si la quantité est supérieure à zéro, le produit est ajouté ou mis à jour.
     * Si la quantité est égale à zéro, le produit est supprimé du panier.
     * Si le panier devient vide après l'opération, il est supprimé de la base de données.
     * @param User $user L'utilisateur pour lequel le panier est géré
     * @param Product $product Le produit à ajouter, mettre à jour ou supprimer
     * @param int $quantity La quantité du produit à ajouter ou mettre à jour (0 pour supprimer)
     *
     */
    public function addOrUpdateItem(User $user, Product $product, int $quantity): void
    {
        // 1. Récupérer ou créer le panier de l'utilisateur
        $basket = $user->getBasket();
        if (!$basket) {
            $basket = new Basket();
            $basket->setUser($user);
            $basket->setCreatedAt(new \DateTimeImmutable());
            $this->entityManager->persist($basket);
        } else {
            $basket->setUpdatedAt(new \DateTimeImmutable());
        }

        // 2. Vérifier si le produit est déjà présent dans le panier
        $basketProductRepository = $this->entityManager->getRepository(BasketProduct::class);
        $basketProduct = $basketProductRepository->findOneBy([
            'basket' => $basket,
            'product' => $product,
        ]);

        if ($basketProduct) {
            // Le produit est déjà dans le panier
            if ($quantity <= 0) {
                // Si quantité <= 0, on supprime la ligne
                $this->entityManager->remove($basketProduct);
                $basket->getBasketProducts()->removeElement($basketProduct);
            } else {
                // Sinon, on met à jour la quantité
                $basketProduct->setQuantity($quantity);
            }
        } else {
            // Le produit n'y est pas, on l'ajoute si la quantité est positive
            if ($quantity > 0) {
                $basketProduct = new BasketProduct();
                $basketProduct->setBasket($basket);
                $basketProduct->setProduct($product);
                $basketProduct->setQuantity($quantity);

                $this->entityManager->persist($basketProduct);
                $basket->getBasketProducts()->add($basketProduct);
            }
        }

        // 3. Enregistrement en base
        $this->entityManager->flush();

        // 4. Si le panier est complètement vide, on le supprime
        if ($basket->getBasketProducts()->isEmpty()) {
            $this->entityManager->remove($basket);
            $this->entityManager->flush();
        }
    }

    /**
     * Retourne les détails du panier d'un utilisateur (items avec sous-totaux et total général)
     * @param User $user L'utilisateur pour lequel on récupère les détails du panier
     * @return array Un tableau contenant les items du panier et le total général
     */
    public function getBasketDetails(User $user): array
    {
        $basket = $user->getBasket();
        $basketItems = [];
        $totalGeneral = 0.0;

        if ($basket) {
            foreach ($basket->getBasketProducts() as $basketProduct) {
                $product = $basketProduct->getProduct();
                $quantity = $basketProduct->getQuantity();

                $subTotal = (float) $product->getPriceCurrent() * $quantity;
                $totalGeneral += $subTotal;

                $basketItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subTotal' => $subTotal,
                ];
            }
        }

        return [
            'basketItems' => $basketItems,
            'totalGeneral' => $totalGeneral,
        ];
    }

    /**
     * Retourne le nombre total d'articles dans le panier de l'utilisateur
     * @param User $user L'utilisateur pour lequel on calcule le total d'articles
     * @return int Le nombre total d'articles dans le panier
     */
    public function getTotalQuantity(User $user): int
    {
        $basket = $user->getBasket();
        if (!$basket) {
            return 0;
        }

        $totalQuantity = 0;
        foreach ($basket->getBasketProducts() as $basketProduct) {
            $totalQuantity += $basketProduct->getQuantity();
        }

        return $totalQuantity;
    }

    /**
     * Supprime le panier d'un utilisateur.
     * Si le panier existe, il est supprimé de la base de données.
     * @param User $user L'utilisateur pour lequel le panier doit être supprimé
     */
    public function clearBasket(User $user): void
    {
        $basket = $user->getBasket();
        if ($basket) {
            // Supprime le panier (grâce à orphanRemoval: true sur basketProducts, les lignes associées seront supprimées automatiquement)
            $this->entityManager->remove($basket);
            $this->entityManager->flush();
        }
    }
}
