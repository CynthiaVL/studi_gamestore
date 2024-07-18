<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/basket', name: 'app_basket')]
    public function basket(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $orders = $entityManager->getRepository(Order::class)->findBy([
            'user' => $user,
            'status' => 'inBasket'
        ]);

        if ($user instanceof User){
            $store = $user->getStore();
            $storeName = $store->getName();
        }

        $totalPrice = 0;

        foreach ($orders as $order) {
            if($order->getGame()->getPromotion() !== null)
            {
                $totalPrice += $order->getGame()->getPromotion() * $order->getQuantity(); 
            }else{
                $totalPrice += $order->getGame()->getPrice() * $order->getQuantity();
            }
        }
    
        return $this->render('order/index.html.twig', [
            'orders' => $orders,
            'totalPrice' => $totalPrice,
            'storeName' => $storeName,
        ]);
    }

    #[Route('/validateBasket', name : 'app_validate_basket', methods: ['POST','GET'])]
    public function validateBasket(OrderRepository $orderRepository, EntityManagerInterface $entityManager)
    {
        $user = $this->getUser();
        $orders = $orderRepository->findBy([
            'user' => $user,
            'status' => 'inBasket'
        ]);

        foreach ($orders as $order) {
            $order->setStatus('validated');
            $entityManager->persist($order);
        }

        $entityManager->flush();

        return $this->redirectToRoute('app_basket');
    }

    #[Route('/history', name: 'app_history')]
    public function orderHistory(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        if ($user instanceof User){
            $store = $user->getStore();
            $storeName = $store->getName();
        }
        $orders = $orderRepository->findBy(['user' => $user]);
        $totalPrice = 0;

        foreach ($orders as $order) {
            if($order->getGame()->getPromotion() !== null)
            {
                $totalPrice += $order->getGame()->getPromotion() * $order->getQuantity(); 
            }else{
                $totalPrice += $order->getGame()->getPrice() * $order->getQuantity();
            }
        }

        return $this->render('order/history.html.twig', [
            'orders' => $orders,
            'totalPrice' => $totalPrice,
            'storeName' => $storeName,
        ]);
    }
}
