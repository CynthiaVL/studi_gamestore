<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChoiseStoreType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request, User $user): Response
    {
        if ($user) {
            if ($user->getStore() !== null) {
                return $this->redirectToRoute('home');
            } else {
                $form = $this->createForm(ChoiseStoreType::class, $user);
                $form->handleRequest($request);
    
                return $this->render('user/choise_store.html.twig', [
                    'choiseStoreForm' => $form->createView(),
                ]);
            }
        }

        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
    }
}
