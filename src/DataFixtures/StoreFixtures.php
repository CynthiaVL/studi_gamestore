<?php

namespace App\DataFixtures;

use App\Entity\Store;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class StoreFixtures extends Fixture implements DependentFixtureInterface{
    public function load(ObjectManager $manager)
    {
        $adresses = [
            'Bordeaux' => $this->getReference('Adress-Bordeaux'),
            'Paris' => $this->getReference('Adress-Paris'),
            'Toulouse' => $this->getReference('Adress-Toulouse'),
            'Lille' => $this->getReference('Adress-Lille'),
            'Nantes' => $this->getReference('Adress-Nantes'),
        ];

        $openTime = new \DateTime('09:00');
        $closeTime = new \DateTime('19:00');

        foreach ($adresses as $city => $adress) {
            $store = (new Store())
            ->setName('GameStore - '. $city)
            ->setAdress($adress)
            ->setOpenTime($openTime)
            ->setCloseTime($closeTime)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setImage('/store/' . strtolower($city) . '.jpg')
            ->setImageUrl('/images/store/' . strtolower($city) . '.jpg');;

            $manager->persist($store);
            $this->addReference("Store-" .$city, $store);
        }

        $manager->flush();
    }

    
    public function getDependencies()
    {
        return [
            AdressFixtures::class,
        ];
    }
}