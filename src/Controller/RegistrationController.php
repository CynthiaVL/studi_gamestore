<?php

namespace App\Controller;

use App\Entity\Adress;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\StoreRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RegistrationController extends AbstractController
{
    private $storeRepository;
    private $httpClient;
    private $googleApiKey;

    public function __construct(StoreRepository $storeRepository, HttpClientInterface $httpClient, string $googleApiKey)
    {
        $this->storeRepository = $storeRepository;
        $this->httpClient = $httpClient;
        $this->googleApiKey = $googleApiKey;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hasher le mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $form->get('plainPassword')->getData());
            $user->setPassword($hashedPassword);

            // Définit d'autres attributs de l'utilisateur si nécessaire
            $selectedRoles = $form->get('roles')->getData();
            $user->setRoles($selectedRoles);

            // Mettre à jour les coordonnées de l'Addresse si nécessaire
            $Adress = $user->getAdress();
            dump($Adress); // Vérifiez l'Addresse avant la mise à jour
            $this->updateAddressCoordinates($Adress, $entityManager);
            dump($Adress); // Vérifiez l'Addresse après la mise à jour            
            if ($Adress->getLatitude() !== null && $Adress->getLongitude() !== null) {
                $nearestStore = $this->findNearestStore($Adress);
                $user->setStore($nearestStore);
            }
            // Persiste l'utilisateur en base de données
            $entityManager->persist($user);
            $entityManager->flush();

            // Redirige après l'inscription, ajustez selon vos besoins
            return $this->redirectToRoute('app_login');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    private function updateAddressCoordinates(Adress $Adress, EntityManagerInterface $entityManager): void
    {
        if ($Adress->getLatitude() === null || $Adress->getLongitude() === null) {
            // Utilisation de l'API Google Maps pour géocoder l'Addresse
            $response = $this->httpClient->request('GET', 'https://maps.googleapis.com/maps/api/geocode/json', [
                'query' => [
                    'Address' => $Adress->getStreet() . ', ' . $Adress->getCity(),
                    'key' => $this->googleApiKey,
                ],
            ]);
    
            $content = $response->getContent();
            $body = json_decode($content);
    
            if (isset($body->results[0]->geometry->location)) {
                $location = $body->results[0]->geometry->location;
                dump($location); // Vérifiez les coordonnées récupérées
    
                $Adress->setLatitude($location->lat)
                        ->setLongitude($location->lng);
    
                // Persistez les changements en base de données
                $entityManager->persist($Adress);
                $entityManager->flush();
    
                dump($Adress); // Vérifiez l'Addresse mise à jour
            } else {
                // Gestion de l'erreur si le géocodage échoue
                $this->addFlash('error', 'Unable to geocode the Address.');
            }
        }
    }
    

    private function findNearestStore(Adress $Adress)
    {
        // Implémentation de la recherche du magasin le plus proche
        // Utilisez votre logique pour trouver le magasin le plus proche en fonction des coordonnées de l'Addresse
        return $this->storeRepository->findNearestStore($Adress->getLatitude(), $Adress->getLongitude());
    }
}
