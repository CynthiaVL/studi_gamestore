<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\GameRepository;
use App\Repository\OrderRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/order')]
class OrderController extends AbstractController
{
    #[Route('/basket', name: 'app_basket')]
    public function basket(EntityManagerInterface $entityManager, GameRepository $gameRepository): Response
    {
        $user = $this->getUser();
        $games = $gameRepository->findAll();
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
            'games' => $games,
            'totalPrice' => $totalPrice,
            'storeName' => $storeName,
        ]);
    }

    #[Route('/update-quantity/{id}', name: 'app_update_quantity', methods: ['POST'])]
    public function updateQuantity(Order $order, Request $request, EntityManagerInterface $entityManager): Response
    {
        $quantity = $request->request->get('quantity');
        $order->setQuantity($quantity);

        $entityManager->persist($order);
        $entityManager->flush();

        return $this->redirectToRoute('app_basket');
    }

    #[Route('/remove-from-basket/{id}', name: 'app_remove_from_basket', methods: ['POST'])]
    public function removeFromBasket(Order $order, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($order);
        $entityManager->flush();

        return $this->redirectToRoute('app_basket');
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

    #[Route('/delivredOrder/{id}', name : 'app_delivred_order', methods: ['POST'])]
    public function delivredOrder(Order $order, EntityManagerInterface $entityManager)
    {
        $order->setStatus('delivred');
        $entityManager->persist($order);
        $entityManager->flush();
    
        return $this->redirectToRoute('app_dashboard');
    }

    #[Route('/remove-order/{id}', name: 'app_remove_order', methods: ['POST'])]
    public function removeOrder(Order $order, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($order);
        $entityManager->flush();

        return $this->redirectToRoute('app_dashboard');
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

    #[Route('/dashboard', name: 'app_dashboard')]
    public function validatedOrders(OrderRepository $orderRepository): Response
    {
        $user = $this->getUser();
        if ($user instanceof User){
            $store = $user->getStore();  
            $orders = $orderRepository->findValidatedOrdersByStore($store);
        }

        $totalPrice =0;

        foreach ($orders as $order) {
            if($order->getGame()->getPromotion() !== null)
            {
                $totalPrice += $order->getGame()->getPromotion() * $order->getQuantity(); 
            }else{
                $totalPrice += $order->getGame()->getPrice() * $order->getQuantity();
            }
        }
    
        return $this->render('order/dashboard.html.twig', [
            'orders' => $orders,
            'totalPrice' => $totalPrice,
        ]);
    }

    #[Route('/dashborad/admin', name: 'app_dashboard_admin')]
    public function totalSales(OrderRepository $orderRepository)
    {
        $user = $this->getUser();
        if ($user instanceof User){
            $store = $user->getStore();  
            $orders = $orderRepository->findAllOrderDelivred($store);
        }

        $totalPrice =0;

        foreach ($orders as $order) {
            if($order->getGame()->getPromotion() !== null)
            {
                $totalPrice += $order->getGame()->getPromotion() * $order->getQuantity(); 
            }else{
                $totalPrice += $order->getGame()->getPrice() * $order->getQuantity();
            }
        }
    
        return $this->render('order/dashboard_admin.html.twig', [
            'orders' => $orders,
            'totalPrice' => $totalPrice,
        ]);
    }
}