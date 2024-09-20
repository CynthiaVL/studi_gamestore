<?php

namespace App\DataFixtures;

use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Récupérer les utilisateurs, jeux, et magasins déjà créés via les autres fixtures
        $users = [
            $this->getReference('user-2'),
            $this->getReference('user-3'),
            $this->getReference('user-4'),
            $this->getReference('user-5'),
            $this->getReference('user-6'),
            $this->getReference('user-7'),
            $this->getReference('user-8'),
        ];

        $games = [
            'game-mario' => $this->getReference('game-Mario'),
            'game-zelda' => $this->getReference('game-Zelda'),
            'game-call-of-duty' => $this->getReference('game-Call-of-Duty'),
            'game-fifa' => $this->getReference('game-FIFA-2023'),
            'game-the-witcher' => $this->getReference('game-The-Witcher-3'),
            'game-minecraft' => $this->getReference('game-Minecraft'),
        ];

        $stores = [
            'Bordeaux' => $this->getReference('Store-Bordeaux'),
            'Paris' => $this->getReference('Store-Paris'),
            'Toulouse' => $this->getReference('Store-Toulouse'),
            'Lille' => $this->getReference('Store-Lille'),
            'Nantes' => $this->getReference('Store-Nantes'),
        ];

        $statuses = ['pending', 'completed', 'canceled'];

        for ($i = 0; $i < 10; $i++) {
            $orderDate = new \DateTime('now - ' . rand(1, 30) . ' days');
            $pickupDate = (clone $orderDate)->modify('+ ' . rand(1, 5) . ' days');

            $order = (new Order())
                ->setUser($users[array_rand($users)])
                ->setGame($games[array_rand($games)])
                ->setStore($stores[array_rand($stores)])
                ->setOrderDate($orderDate)
                ->setPickupdate($pickupDate)
                ->setStatus($statuses[array_rand($statuses)])
                ->setQuantity(rand(1, 3))
                ->setCreatedAt(new \DateTimeImmutable());

            $manager->persist($order);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
            GameFixtures::class,
            StoreFixtures::class,
        ];
    }
}
