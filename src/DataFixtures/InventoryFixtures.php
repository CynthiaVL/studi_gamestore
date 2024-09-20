<?php

namespace App\DataFixtures;

use App\Entity\Inventory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class InventoryFixtures extends Fixture implements DependentFixtureInterface{
    public function load(ObjectManager $manager)
    {
        $stores = [
            'Bordeaux' => $this->getReference('Store-Bordeaux'),
            'Paris' => $this->getReference('Store-Paris'),
            'Lyon' => $this->getReference('Store-Toulouse'),
            'Marseille' => $this->getReference('Store-Lille'),
            'Nantes' => $this->getReference('Store-Nantes'),
        ];

        $games = [
            'game-mario' => $this->getReference('game-Mario'),
            'game-zelda' => $this->getReference('game-Zelda'),
            'game-call-of-duty' => $this->getReference('game-Call-of-Duty'),
            'game-fifa' => $this->getReference('game-FIFA-2023'),
            'game-the-witcher' => $this->getReference('game-The-Witcher-3'),
            'game-minecraft' => $this->getReference('game-Minecraft'),
        ];

        foreach ($stores as $storeKey => $store) {
            foreach ($games as $gameKey => $game) {
                $quantity = rand(10, 50);
                
                $inventory = (new Inventory())
                    ->setStore($store)
                    ->setGame($game)
                    ->setQuantity($quantity);

                // Persister chaque inventaire
                $manager->persist($inventory);
            }
            $manager->flush();
        }
    }

    public function getDependencies()
    {
        // Dépendance aux fixtures Store et Game pour les récupérer par référence
        return [
            StoreFixtures::class,
            GameFixtures::class,
        ];
    }
}