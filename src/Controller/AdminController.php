<?php

namespace App\Controller;

use App\Form\UserRoleType;
use App\Repository\ActionRepository;
use App\Repository\AnnonceRepository;
use App\Repository\CategorieRepository;
use App\Repository\ContactRepository;
use App\Repository\InfoRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(): Response
    {
        // Vérification que l'utilisateur est connecté et qu'il a le rôle admin
        if ($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
            return $this->render('admin/index.html.twig', [
                'controller_name' => 'AdminController',
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    // Gestion des USERS
    // Affichage de tous les utilisateurs
    #[Route('/admin/gestionUsers', name: 'gestion_users')]
    public function manageUsers(UserRepository $userRepository): Response
    {
        if ($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
            // récupérer la liste des utilisateurs depuis la base de données
            $users = $userRepository->findAll();
            return $this->render('admin/gestionUsers.html.twig', [
                'users' => $users,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    // Modifier le rôle d'un utilisateur
    #[Route('/admin/gestionUser/{id}/role', name: 'gestion_users_role', methods: ['GET', 'POST'])]
    public function updateUserRole(Request $request, int $id, UserRepository $userRepository): Response
    {
        // Vérification pour sécuriser la tâche
        if (!$this->getUser() || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }
        // Récupérer l'utilisateur par ID
        $user = $userRepository->find($id);
        // Création du formulaire
        $form = $this->createForm(UserRoleType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Mettre à jour les rôles de l'utilisateur à partir des données du formulaire
            $roles = $form->get('roles')->getData();
            $user->setRoles($roles);  // Assigner les nouveaux rôles à l'utilisateur

            // Sauvegarder les modifications
            $userRepository->save($user, true);
            $this->addFlash('success', 'Le role a été mofifié');

            // Redirection après la mise à jour des rôles
            return $this->redirectToRoute('gestion_users');
        }

        return $this->render('admin/updateUserRole.html.twig', [
            'user' => $user,
            'updateUserRoleForm' => $form->createView(),
        ]);
    }

    // Gestion des actions

    //affichage des actions coté admin
    #[Route('/admin/gestionActions', name: 'gestion_actions')]
    public function manageActions(ActionRepository $actionsRepo): Response
    {
        if ($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
            // récupérer la liste des utilisateurs depuis la base de données
            $actions = $actionsRepo->findAll();
            return $this->render('admin/gestionActions.html.twig', [
                'actions' => $actions,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    
 /// Gestion Catégories

 // afficher toutes les catégories
 #[Route('/admin/gestionCategories', name: 'gestion_categories')]
    public function manageCategories( CategorieRepository $catRepo): Response
    {
        if ($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
            $categories = $catRepo->findAll();
            return $this->render('admin/gestionCategories.html.twig', [
                'categories' => $categories,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    }

    // Gestion des annonces

    #[Route('/admin/gestionAnnonces' , name: 'gestion_annonces')]
    public function manageAnnonces(AnnonceRepository $annonceRepo): Response
    {
        if ($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
            $annonces = $annonceRepo->findAll();
            
            return $this->render('admin/gestionAnnonces.html.twig', [
               'annonces'=>$annonces,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    } 

    //Gestion des infos
    #[Route('/admin/gestionInfos' , name: 'gestion_infos')]
    public function manageInfos(InfoRepository $infoRepo): Response
    {
        if ($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
            $infos = $infoRepo->findAll();
            
            return $this->render('admin/gestionInfos.html.twig', [
               'infos'=>$infos,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    } 
    //Gestion des messages
    #[Route('/admin/gestionMessages' , name: 'gestion_messages')]
    public function manageContacts(ContactRepository $contactRepo): Response
    {
        if ($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
            $contacts = $contactRepo->findAllOrderedByNewest();
            
            return $this->render('admin/gestionContacts.html.twig', [
               'contacts'=>$contacts,
            ]);
        } else {
            return $this->redirectToRoute('app_login');
        }
    } 
    }

