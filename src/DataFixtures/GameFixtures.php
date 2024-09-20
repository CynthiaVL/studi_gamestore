<?php

namespace App\DataFixtures;

use App\Entity\Game;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GameFixtures extends Fixture {
    public function load(ObjectManager $manager)
    {
        $games = [
            [
                'title' => 'Mario',
                'content' => 'Jeu tout public, venez découvrir ou redécouvrir ce jeu iconique.',
                'pegi' => 3,
                'genre' => ['plateforme', 'aventure'],
                'price' => 10,
                'promotion' => null,
                'platform' => ['ordinateur', 'wii', 'PSP'],
                'releaseDate' => '1998-01-01',
                'image' => 'mario.jpg',
            ],
            [
                'title' => 'Zelda',
                'content' => 'Explorez le royaume d\'Hyrule dans cette aventure épique.',
                'pegi' => 12,
                'genre' => ['aventure', 'action'],
                'price' => 30,
                'promotion' => 25,
                'platform' => ['Switch', 'Wii'],
                'releaseDate' => '2017-03-03',
                'image' => 'zelda.jpg',
            ],
            [
                'title' => 'Call of Duty',
                'content' => 'Jeu de tir à la première personne avec des missions intenses.',
                'pegi' => 18,
                'genre' => ['action', 'tir'],
                'price' => 60,
                'promotion' => null,
                'platform' => ['PC', 'PS5', 'Xbox'],
                'releaseDate' => '2020-11-13',
                'image' => 'call_of_duty.jpg',
            ],
            [
                'title' => 'FIFA 2023',
                'content' => 'Le dernier opus du célèbre jeu de football avec des graphismes encore améliorés.',
                'pegi' => 3,
                'genre' => ['sport'],
                'price' => 50,
                'promotion' => 40,
                'platform' => ['PC', 'PS5', 'Xbox'],
                'releaseDate' => '2023-09-30',
                'image' => 'fifa2023.jpg',
            ],
            [
                'title' => 'The Witcher 3',
                'content' => 'Jeu de rôle en monde ouvert avec des graphismes époustouflants.',
                'pegi' => 16,
                'genre' => ['RPG', 'aventure'],
                'price' => 40,
                'promotion' => null,
                'platform' => ['PC', 'PS4', 'Switch'],
                'releaseDate' => '2015-05-19',
                'image' => 'the_witcher_3.jpg',
            ],
            [
                'title' => 'Minecraft',
                'content' => 'Jeu créatif où vous pouvez construire tout ce que vous voulez.',
                'pegi' => 7,
                'genre' => ['créatif', 'sandbox'],
                'price' => 20,
                'promotion' => 15,
                'platform' => ['PC', 'Xbox', 'Switch'],
                'releaseDate' => '2011-11-18',
                'image' => 'minecraft.jpg',
            ],
        ];

        foreach ($games as $gameData) {
            $game = (new Game())
                ->setTitle($gameData['title'])
                ->setContent($gameData['content'])
                ->setPegi($gameData['pegi'])
                ->setGenre($gameData['genre'])
                ->setPrice($gameData['price'])
                ->setPromotion($gameData['promotion'])
                ->setPlatform($gameData['platform'])
                ->setReleaseDate(new \DateTime($gameData['releaseDate']))
                ->setImage($gameData['image']);

            $manager->persist($game);
            $this->addReference("game-" .strtolower(str_replace(' ', '-', $gameData['title'])), $game);
        }

    $manager->flush();
    }
}