<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class UserFixtures extends Fixture implements DependentFixtureInterface{
    
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
    $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $password = 'Test1234';
        for ($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setEmail($faker->unique()->safeEmail)
                ->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setBirthdate($faker->dateTimeBetween('-60 years', '-18 years'))
                ->setAdress($this->getReference('adress-' . strtolower($faker->city)))
                ->setPassword($this->passwordHasher->hashPassword($user, $password));

            if ($i === 0) {
                $user->setRoles(['ROLE_ADMIN']);
            } elseif ($i === 1 ){
                $user->setRoles(['ROLE_STAFF']);
            }else {
                $user->setRoles(['ROLE_USER']);
            }

            $manager->persist($user);
            $this->addReference('user-' . $i, $user);
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

