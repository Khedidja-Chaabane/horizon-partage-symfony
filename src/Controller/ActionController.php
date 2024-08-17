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

    // Affichage d'une action spécifique
    #[Route('/action/{id}', name: 'show_action')]
    public function showAction(int $id, ActionRepository $actionRepo): Response
    {
        $action = $actionRepo->find($id);
        if(!$action){
            return  $this->redirectToRoute('app_actions');
        }
        return $this->render('action/showAction.html.twig', [
        'action' => $action
    ]);
    }
}
