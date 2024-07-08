<?php

namespace App\Controller;

use App\Repository\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController 
{
    #[Route('/', name: 'home')]
    public function index(GameRepository $gameRepository): Response
    {
        $promotedGames = $gameRepository->findByPromotion(); // Exemple de méthode à créer dans GameRepository pour récupérer les jeux en promotion
        $recentGames = $gameRepository->findByReleaseDate();

        return $this->render('base.html.twig', [
            'promotedGames' => $promotedGames,
            'recentGames' => $recentGames,
        ]);
    }
}