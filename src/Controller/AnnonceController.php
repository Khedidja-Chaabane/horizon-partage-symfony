<?php

namespace App\Controller;

use App\Entity\Annonce;
use App\Form\AnnonceFilterType;
use App\Form\AnnonceType;
use App\Repository\AnnonceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnnonceController extends AbstractController
{
    #[Route('/annonces', name: 'app_annonces')]
    public function index(AnnonceRepository $annonceRepo, Request $request): Response
    {
        // Créer une variable pour stocker la catégorie sélectionnée
        $categorieName = null;
        // Créer le formulaire
        $form = $this->createForm(AnnonceFilterType::class);
        $form->handleRequest($request);
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
        if ($form->get('offreEmploi')->isClicked()) {
            $categorieName = "offre d'emploi";
        } elseif ($form->get('partenariat')->isClicked()) {
            $categorieName = 'partenariat';
        } elseif ($form->get('benevolat')->isClicked()) {
            $categorieName = 'bénévolat';
        }
    }
        //Récupérer les annonces activées en fonction de la catégorie séléctionnée
        if ($categorieName) {
            $annonces = $annonceRepo->findByCategorieName($categorieName, true);
        } else {
            // Récupération de toutes les annonces ayants le status activé
            $annonces = $annonceRepo->findBy(['status' => true]);
        }

        return $this->render('annonce/index.html.twig', [
            'annonces' => $annonces,
            'annonceFilterForm' => $form->createView(),
            'categorieName' => $categorieName,
        ]);
    }

    //Création d'une nouvelle annonce
    #[Route('/admin/new-annonce', name: 'admin_new_annonce')]
    public function newAnnonce(Request $request, AnnonceRepository $annonceRepo): Response
    {
        // Vérification pour sécuriser la tâche
        if (!$this->getUser() || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $annonce = new Annonce();
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonceRepo->add($annonce, true);
            $this->addFlash('success', 'l\'annonce a bien été ajoutée');
            return $this->redirectToRoute('gestion_annonces');
        }
        return $this->render('admin/newAnnonce.html.twig', [
            'newAnnonceForm' => $form->createView(),
        ]);
    }

    // Modification d'une annonce 
    #[Route('/admin/updateAnnonce/{id}', name: 'admin_update_annonce')]
    public function updateAnnonce(int $id, Request $request, AnnonceRepository $annonceRepo): Response
    {
        // Vérification pour sécuriser la tâche
        if (!$this->getUser() || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }
        // Récupération de l'action
        $annonce = $annonceRepo->find($id);
        if (!$annonce) {
            $this->addFlash('error', 'Annonce non trouvée');
            return $this->redirectToRoute('gestion_annonces');
        }
        $form = $this->createForm(AnnonceType::class, $annonce);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $annonceRepo->add($annonce, true);
            $this->addFlash('success', 'L\'annonce a bien été modifiée');
            return $this->redirectToRoute('gestion_annonces');
        }

        return $this->render('admin/updateAnnonce.html.twig', [
            'updateAnnonceForm' => $form->createView(),
            'annonce' => $annonce,
        ]);
    }

    //Affichage d'une annonce spécifique
    #[Route('annonce/{id}', name: 'showAnnonce')]
    public function showAnnonce(int $id, AnnonceRepository $annonceRepo): Response
    {
        $annonce = $annonceRepo->find($id);
        if (!$annonce) {
            $this->addFlash('error', 'Annonce non trouvée');
            return $this->redirectToRoute('app_annonces');
        }
        return $this->render('annonce/showAnnonce.html.twig', [
            'annonce' => $annonce,
        ]);
    }

    //Suppression d'une annonce
    #[Route('/admin/deleteAnnonce/{id}', name: 'admin_delete_annonce', methods: ['POST'])]
    public function deleteAnnonce(int $id, AnnonceRepository $annonceRepo, Request $request): Response
    {
        // Vérification pour sécuriser la tâche
        if (!$this->getUser() || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }
        $annonce = $annonceRepo->find($id);
        if (!$annonce) {
            $this->addFlash('error', 'Annonce non trouvée');
            return $this->redirectToRoute('gestion_annonces');
        }
        if ($this->isCsrfTokenValid(
            'delete' . $annonce->getId(),
            $request->request->get('_token')
        )) {

            $annonceRepo->remove($annonce, true);
            $this->addFlash('success', 'l\'annonce a bien été supprimée');
        } else {
            $this->addFlash('error', 'l\'annonce ne peut pas etre supprimée');
        }
        return $this->redirectToRoute('gestion_annonces');
    }
}
