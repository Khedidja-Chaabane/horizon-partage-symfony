<?php

namespace App\Controller;

use App\Entity\Action;
use App\Form\ActionType;
use App\Form\UserRoleType;
use App\Repository\ActionRepository;
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

    // creation d'une nouvelle action
    #[Route('/admin/new-action', name: 'admin_new_action')]
    public function newAction(Request $request, ActionRepository $actionRepo): Response
    {
        // Vérification pour sécuriser la tâche
        if (!$this->getUser() || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        $action = new Action();
        $form = $this->createForm(ActionType::class, $action);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $imageFile = $form->get('image')->getData();

                if ($imageFile) {
                    // 1ere étape: définir le nom du fichier
                    $nomImage = date('YmdHis') . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    // 2eme étape : enregistrer le fichier dans le projet
                    $imageFile->move(
                        $this->getParameter('action'), //l'emplacement->dans config/services.yaml à l'option parametres
                        $nomImage
                    );

                    // 3eme étape: faut enregistrer le nom du fichier dans l'objet
                    $action->setImage($nomImage);
                }

                // Enregistrer l'action dans la base de données
                $actionRepo->add($action, true);
                //ajouter une notification
                $this->addFlash('success', 'l\'action a bien été ajoutée');

                //redirection 
                return $this->redirectToRoute('app_actions');
            }
        }

        return $this->render('admin/newAction.html.twig', [
            'newActionForm' => $form->createView(),
        ]);
    }

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

    // Modifier une action
    #[Route('admin/action/{id}/update', name: 'admin_update_action')]
    public function updateAction(int $id, Request $request, ActionRepository $actionRepo): Response
    {
        // Vérification pour sécuriser la tâche
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        // Récupération de l'action
        $action = $actionRepo->find($id);
        if (!$action) {
            $this->addFlash('error', 'Action non trouvée');
            return $this->redirectToRoute('gestion_actions');
        }

        // Création du formulaire lié à l'objet $action
        $form = $this->createForm(ActionType::class, $action);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if ($imageFile) {
                $nomImage = date('YmdHis') . '-' . uniqid() . '.' . $imageFile->guessExtension();

                // Enregistrement du fichier dans le répertoire des actions
                $imageFile->move(
                    $this->getParameter('action'),
                    $nomImage
                );

                // Suppression de l'ancienne image s'il y en a une
                if ($action->getImage() && file_exists($this->getParameter('action') . '/' . $action->getImage())) {
                    unlink($this->getParameter('action') . '/' . $action->getImage());
                }

                $action->setImage($nomImage);
            }

            // Enregistrement de l'action
            $actionRepo->add($action, true);
            $this->addFlash('success', 'L\'action a bien été modifiée');

            return $this->redirectToRoute('gestion_actions');
        }

        return $this->render('admin/updateAction.html.twig', [
            'formUpdateAction' => $form->createView(),
            'action' => $action
        ]);
    }
}
