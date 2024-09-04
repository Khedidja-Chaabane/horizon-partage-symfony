<?php

namespace App\Controller;

use App\Repository\InfoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(InfoRepository $infoRepo): Response
    {
        //Affichage des 3 dernieres infos
        $recentInfos = $infoRepo->findRecentInfos(3);
        return $this->render('home/index.html.twig', [
            'recentInfos' => $recentInfos,
        ]);
    }
}
