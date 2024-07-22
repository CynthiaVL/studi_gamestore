<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\User;
use App\Repository\GameRepository;
use App\Repository\InventoryRepository;
use App\Repository\OrderRepository;
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

    #[Route('/add-to-basket/{id}', name: 'app_add_to_basket')]
    public function addToBasket($id, GameRepository $gameRepository, InventoryRepository $inventoryRepository, OrderRepository $orderRepository, EntityManagerInterface $entityManager, Request $request): Response
    {
        $game = $gameRepository->find($id);
        $user = $this->getUser();
        $orders = $orderRepository->findAll();
    
        if ($user instanceof User) {
            $store = $user->getStore();
            $storeName = $store->getName();
        }
        
        if (!$game || !$user || !$store) {
            throw $this->createNotFoundException('Le jeu, l\'utilisateur ou le magasin n\'existe pas.');
        }
        
        $inventory = $inventoryRepository->findOneBy([
            'game' => $game,
            'store' => $store,
        ]);
    
        if (!$inventory || $inventory->getQuantity() <= 0) {
            throw $this->createNotFoundException('Le jeu n\'est pas disponible dans ce magasin.');
        }
    
        $quantity = $request->request->get('quantity', 1);
    
        if ($quantity > $inventory->getQuantity()) {
            throw new \Exception('La quantité demandée dépasse la quantité disponible.');
        }
    
        $order = new Order();
        $order->setGame($game)
              ->setUser($user)
              ->setStore($store)
              ->setStatus('inBasket')
              ->setQuantity($quantity)
              ->setOrderDate(new \DateTime())
              ->setPickupdate($orderRepository->calculatePickupDate())
              ->setCreatedAt(new \DateTime());
    
        $inventory->setQuantity($inventory->getQuantity() - $quantity);
    
        $entityManager->persist($order);
        $entityManager->flush();

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
            'game' => $game,
            'orders' => $orders,
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
    public function removeFromBasket(Order $order, InventoryRepository $inventoryRepository, EntityManagerInterface $entityManager): Response
    {
        $inventory = $inventoryRepository->findOneBy([
            'game' => $order->getGame(),
            'store' => $order->getStore(),
        ]);
    
        if ($inventory) {
            $inventory->setQuantity($inventory->getQuantity() + $order->getQuantity());
        }

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

    #[Route('/dashboard_admin', name: 'app_dashboard_admin')]
    public function findAllOrdersByStore(OrderRepository $orderRepository, Request $request): Response
    {
        $user = $this->getUser();
        $orders = [];
        
        if ($user instanceof User) {
            $store = $user->getStore();
            $viewAllStores = $request->query->get('view_all_stores', false);
    
            if ($viewAllStores) {
                $orders = $orderRepository->findAllOrderDelivred();
            } else {
                $orders = $orderRepository->findAllOrderDelivred($store);
            }
    
            $totalPrice = 0;
    
            foreach ($orders as $order) {
                if ($order->getGame()->getPromotion() !== null) {
                    $totalPrice += $order->getGame()->getPromotion() * $order->getQuantity(); 
                } else {
                    $totalPrice += $order->getGame()->getPrice() * $order->getQuantity();
                }
            }
        
            return $this->render('order/dashboard_admin.html.twig', [
                'orders' => $orders,
                'totalPrice' => $totalPrice,
                'viewAllStores' => $viewAllStores,
            ]);
        }
    
        throw $this->createAccessDeniedException('Vous n\avez pas les accès pour cette page.');
    }

    #[Route('/sales-details/{title}', name: 'app_sales_details')]
    public function salesDetails(string $title, OrderRepository $orderRepository): Response
    {
        $orders = $orderRepository->findByGameTitle($title);
        $details = [];

        foreach ($orders as $order) {
            $details[] = [
                'date' => $order->getOrderDate(),
                'quantity' => $order->getQuantity(),
                'totalPrice' => $order->getGame()->getPromotion() !== null ? $order->getGame()->getPromotion() * $order->getQuantity() : $order->getGame()->getPrice() * $order->getQuantity()
            ];
        }

        return $this->render('order/sales_details.html.twig', [
            'title' => $title,
            'details' => $details,
        ]);
    }

}