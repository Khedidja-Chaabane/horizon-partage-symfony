<?php

namespace App\Controller;

use App\Repository\ActionRepository;
use App\Repository\InfoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(ActionRepository $actionRepo, InfoRepository $infoRepo): Response
    {
        //affichage des actions
        $actions = $actionRepo->findAll();
        //Affichage des 5 dernieres infos
        $recentInfos = $infoRepo->findRecentInfos(5);
        return $this->render('home/index.html.twig', [
            'recentInfos' => $recentInfos,
            'actions' => $actions ,
        ]);
    }
}
