<?php

namespace App\DataFixtures;

use App\Entity\Adress;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AdressFixtures extends Fixture{
    public function load(ObjectManager $manager)
    {
        $adresses = [
            'Bordeaux' => [   
                'street' => '33 avenue du Gaming',
                'city' => 'Bordeaux',
                'postalCode' => '33000',
                'latitude' => 44.8364835,
                'longitude' => -0.5765626,
            ],
            'Toulouse' => [
                'street' => '31 Boulevard du Geek',
                'city' => 'Toulouse',
                'postalCode' => '31000',
                'latitude' => 43.606686,
                'longitude' => 1.440322,
            ],
            'Paris' => [
                'street' => '75 Allée des Nolife',
                'city' => 'Paris',
                'postalCode' => '75000',
                'latitude' => 48.854086,
                'longitude' => 2.311989,
            ],
            'Nantes' => [
                'street' => '44 Rue des Manettes',
                'city' => 'Nantes',
                'postalCode' => '44000',
                'latitude' => 47.21454,
                'longitude' => -1.558046,
            ],
            'Lille' => [
                'street' => '59 Chemin des Jeux',
                'city' => 'Lille',
                'postalCode' => '59000',
                'latitude' => 50.630529,
                'longitude' => 3.057235,
            ],
        ];

        foreach ($adresses as $key => $data) {
            $adress = (new Adress())
            ->setStreet($data['street'])
            ->setCity($data['city'])
            ->setPostalCode($data['postalCode'])
            ->setLatitude($data['latitude'])
            ->setLongitude($data['longitude']);

            $manager->persist($adress);
            $this->addReference("Adress-" .$data['city'], $adress);
        }
        $manager->flush();
    }

}