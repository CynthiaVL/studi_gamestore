<?php

namespace App\Controller;

use App\Entity\Game;
use App\Entity\Store;
use App\Entity\User;
use App\Form\GameType;
use App\Repository\GameRepository;
use App\Repository\StoreRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/game')]
class GameController extends AbstractController
{
    #[Route('/', name: 'app_game_index', methods: ['GET', 'POST'])]
    public function index(GameRepository $gameRepository, StoreRepository $storeRepository, UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        {
            $user = $this->getUser();
            // Récupération de tous les jeux
            $games = $gameRepository->findAll();
    
            // Récupération de tous les magasins
            $stores = $storeRepository->findAll();

            //Récupération du magasin par défaut
            $nearestStoreByUser = $userRepository->findStoreByUser($user);
    
            // Si la requête est en méthode POST, cela signifie que le formulaire de choix de magasin a été soumis
            if ($request->isMethod('POST')) {
                $storeId = $request->request->get('ChoiceStore');
    
                // Récupération de l'entité Store correspondant à l'ID sélectionné
                $store = $entityManager->getRepository(Store::class)->find($storeId);
    
                if (!$store) {
                    throw $this->createNotFoundException('The store does not exist.');
                }
    
                /** @var User $user */
                $user->setStore($store);
    
                $entityManager->flush();
    
                // Redirection vers la même page après la sélection du magasin
                return $this->redirectToRoute('app_game_index');
            }
    
            // Affichage de la page d'accueil des jeux avec les jeux et les magasins disponibles
            return $this->render('game/index.html.twig', [
                'games' => $games,
                'stores' => $stores,
                'nearestStoreByUser' => $nearestStoreByUser, 
            ]);
        }
    }

    #[Route('/new', name: 'app_game_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $game = new Game();
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image= $form->get('image')->getData();

            if ($image) {
                $newFilename = uniqid().'.'.$image->guessExtension();

                try {
                    $image->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gérer l'exception si nécessaire
                }

                $game->setImage($newFilename);
            }

            $entityManager->persist($game);
            $entityManager->flush();

            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/new.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_show', methods: ['GET'])]
    public function show(Game $game): Response
    {
        return $this->render('game/show.html.twig', [
            'game' => $game,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_game_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(GameType::class, $game);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('game/edit.html.twig', [
            'game' => $game,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_game_delete', methods: ['POST'])]
    public function delete(Request $request, Game $game, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$game->getId(), $request->request->get('_token'))) {
            $entityManager->remove($game);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_game_index', [], Response::HTTP_SEE_OTHER);
    }
}
