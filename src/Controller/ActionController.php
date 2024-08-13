<?php

namespace App\Controller;

use App\Repository\ActionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActionController extends AbstractController
{
    #[Route('/actions', name: 'app_actions')]
    public function index(ActionRepository $actionRepo): Response
    {
         // Affichage de toutes les actions
         $actions = $actionRepo->findAll();
        return $this->render('action/index.html.twig', [
            'actions' => $actions,
        ]);
    }
}
