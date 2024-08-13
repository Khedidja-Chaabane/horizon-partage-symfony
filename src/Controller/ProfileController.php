<?php

namespace App\Controller;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
         // Vérification que l'utilisateur est connecté
         if (!$this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Affichage de la page de profil et Passage de l'utilisateur à la vue Twig sous la variable user
        return $this->render('profile/index.html.twig', [
            'user' => $this->getUser(),
        ]);
    }
}
