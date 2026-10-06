<?php

namespace App\Service;

use App\Entity\SalesOrder;
use App\Entity\SalesOrderProduct;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class OrderService
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
    }

    /**
     * Crée une commande à partir du panier de l'utilisateur.
     *
     * @param User $user L'utilisateur pour lequel créer la commande
     * @return SalesOrder La commande créée
     *
     * @throws \LogicException Si le panier est vide.
     */
    public function createOrderFromBasket(User $user): SalesOrder
    {
        // 1. Récupérer le panier de l'utilisateur
        $basket = $user->getBasket();

        // 2. Vérifier si le panier est vide
        if (!$basket || $basket->getBasketProducts()->isEmpty()) {
            throw new \LogicException('Impossible de créer une commande : votre panier est vide.');
        }

        // 3. Créer une nouvelle commande
        $order = new SalesOrder();
        $order->setUser($user);
        $order->setCreatedAt(new \DateTimeImmutable());

        $totalPrice = 0;

        // 4. Parcourir les produits du panier et créer les lignes de commande correspondantes
        foreach ($basket->getBasketProducts() as $basketProduct) {
            $product = $basketProduct->getProduct();
            $quantity = $basketProduct->getQuantity();

            $orderProduct = new SalesOrderProduct();
            $orderProduct->setSalesOrder($order);           // Associer le produit à la commande
            $orderProduct->setQuantity($quantity);          // Définir la quantité commandée
            $orderProduct->setName($product->getName());    // Définir le nom du produit au moment de la commande
            $orderProduct->setHistoricalPrice($product->getPriceCurrent()); // Définir le prix historique du produit au moment de la commande
            $orderProduct->setSku($product->getSku());      // Définir le SKU du produit au moment de la commande
            $orderProduct->setEan13($product->getEan13());  // Définir le code EAN13 du produit au moment de la commande
            $orderProduct->setHistoricalIdProduct($product->getId()); // Définir l'ID historique du produit au moment de la commande


            // Persister l'entité SalesOrderProduct
            $this->entityManager->persist($orderProduct);

            $totalPrice += $product->getPriceCurrent() * $quantity;
        }

        // 5. Définir le prix total de la commande
        $order->setTotalPriceHistorical($totalPrice);
        $this->entityManager->persist($order);

        // 6. Supprimer le panier courant
        $this->entityManager->remove($basket);

        // 7. Exécuter toutes les requêtes SQL en une seule fois
        $this->entityManager->flush();

        return $order;
    }
}
