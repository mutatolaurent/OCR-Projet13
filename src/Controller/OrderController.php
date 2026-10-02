<?php

namespace App\Controller;

use App\Entity\SalesOrder;
use App\Entity\SalesOrderProduct;
use App\Service\BasketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class OrderController extends AbstractController
{
    #[Route('/order/create', name: 'app_order_create')]
    public function create(EntityManagerInterface $entityManager, BasketService $basketService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $basket = $user->getBasket();

        if (!$basket || $basket->getBasketProducts()->isEmpty()) {
            $this->addFlash('danger', 'Votre panier est vide.');
            return $this->redirectToRoute('app_account');
        }

        // 1. Créer une nouvelle commande
        $order = new SalesOrder();
        $order->setUser($user);
        $order->setCreatedAt(new \DateTimeImmutable());
        $order->setStatus('pending'); // Ou un autre statut initial

        $totalPrice = 0;

        foreach ($basket->getBasketProducts() as $basketProduct) {
            $product = $basketProduct->getProduct();
            $quantity = $basketProduct->getQuantity();

            $orderProduct = new SalesOrderProduct();
            $orderProduct->setSalesOrder($order);
            $orderProduct->setProduct($product);
            $orderProduct->setQuantity($quantity);
            $orderProduct->setPrice($product->getPriceCurrent()); // Capture le prix actuel au moment de la commande

            $entityManager->persist($orderProduct);
            $order->addSalesOrderProduct($orderProduct);

            $totalPrice += $product->getPriceCurrent() * $quantity;
        }

        $order->setTotalPrice($totalPrice);
        $entityManager->persist($order);

        // 2. Supprimer le panier courant
        $entityManager->remove($basket);

        $entityManager->flush();

        // 3. Générer un message flash de succès
        $this->addFlash('success', 'Votre commande n°' . $order->getId() . ' a été créée avec succès !');

        // 4. Rediriger vers la route app_account
        return $this->redirectToRoute('app_account');
    }
}
