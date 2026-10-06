<?php

namespace App\Controller;

use App\Entity\SalesOrder;
use App\Entity\SalesOrderProduct;
use App\Service\BasketService;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class OrderController extends AbstractController
{
    #[Route('/order/create', name: 'app_order_create')]
    public function create(OrderService $orderService): Response
    {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        try {
            $order = $orderService->createOrderFromBasket($user);
        } catch (\LogicException $e) {
            $this->addFlash('danger', $e->getMessage());
            return $this->redirectToRoute('app_main');
        }

        // Succès
        $this->addFlash('success', 'Votre commande CMD-' . $order->getId() . ' a été créée avec succès !');

        return $this->redirectToRoute('app_account');
    }
}
