<?php

namespace App\Controller;

use App\Form\UserRoleType;
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
            return $this->redirectToRoute('app_home');
        }
    }

    // Gestion des USERS
    // Affichage de tous les utilisateurs
    #[Route('/admin/gestionUsers', name: 'gestion_users')]
public function manageUsers(UserRepository $userRepository):Response
{
    if ($this->getUser() && $this->isGranted('ROLE_ADMIN')) {
        // récupérer la liste des utilisateurs depuis la base de données
        $users = $userRepository->findAll();
            return $this->render('admin/gestionUsers.html.twig', [
                'users' => $users,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
}

// Modifier le rôle d'un utilisateur
#[Route('/admin/gestionUsers/{id}/role', name: 'gestion_users_role', methods: ['GET', 'POST'])]
public function updateUserRole(Request $request, int $id, UserRepository $userRepository): Response
{
    // Vérification pour sécuriser la tâche
    if (!$this->getUser() || !$this->isGranted('ROLE_ADMIN')) {
        return $this->redirectToRoute('app_home');
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

        // Redirection après la mise à jour des rôles
        return $this->redirectToRoute('gestion_users');
    }

    return $this->render('admin/updateUserRole.html.twig', [
        'user' => $user,
        'updateUserRoleForm' => $form->createView(),
    ]);
}

}
