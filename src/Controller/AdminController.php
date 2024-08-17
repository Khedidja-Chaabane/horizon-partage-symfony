<?php

namespace App\Controller;

use App\Entity\Action;
use App\Entity\Categorie;
use App\Form\ActionType;
use App\Form\CategorieType;
use App\Form\UserRoleType;
use App\Repository\ActionRepository;
use App\Repository\CategorieRepository;
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
                    $nomImage = date('YmdHis') . '-' . uniqid() . '.' . $imageFile->getClientOriginalExtension();

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
                return $this->redirectToRoute('gestion_actions');
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
            // Récupération du fichier image soumis par l'utilisateur
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Si une nouvelle image est soumise
                $nomImage = date('YmdHis') . '-' . uniqid() . '.' . $imageFile->getClientOriginalExtension();

                // Enregistrement du fichier dans le répertoire des actions
                $imageFile->move($this->getParameter('action'), $nomImage);

                // Suppression de l'ancienne image
                if ($action->getImage()) {
                    unlink($this->getParameter('action') . '/' . $action->getImage());
                }

                // Mise à jour de l'image dans l'entité
                $action->setImage($nomImage);
            } else {
                // Si aucune nouvelle image n'est soumise, conserver l'image actuelle
                $action->setImage($action->getImage());
            }
            // Enregistrement de l'action
            $actionRepo->add($action, true);
            $this->addFlash('success', 'L\'action a bien été modifiée');

            return $this->redirectToRoute('gestion_actions');
        }

        return $this->render('admin/updateAction.html.twig', [
            'formUpdateAction' => $form->createView(),
            'action' => $action,
            'imagePath' => $this->getParameter('action') . '/' . $action->getImage(), // Chemin de l'image actuelle pour l'affichage
        ]);
    }

    //supprimer une action
    #[Route('admin/action/{id}/delete', name: 'admin_delete_action', methods: ['POST'])]
    public function deleteAction(int $id, ActionRepository $actionRepo, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }
        $action = $actionRepo->find($id);
        if (!$action) {
            return $this->redirectToRoute('gestion_actions');
        }
        if ($this->isCsrfTokenValid(
            'delete' . $action->getId(),
            $request->request->get('_token')
        )) {
            if ($action->getImage()) {
                unlink($this->getParameter('action') . '/' . $action->getImage());
            }
            $actionRepo->remove($action, true);
            $this->addFlash('success', 'l\'action a bien été supprimée');
        } else {
            $this->addFlash('error', 'l\'action ne peut pas etre supprimée');
        }
        return $this->redirectToRoute('gestion_actions');
    }

 /// Partie Catégories
 
 // Créer une nouvelle catégorie
 #[Route ('/admin/new-category', name:'admin_new_category')]
 public function newCategory(Request $request , CategorieRepository $catRepo) : Response
 {
    if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }
        $category = new Categorie();
        $form = $this->createForm(CategorieType::class , $category);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $catRepo->add($category, true);
            $this->addFlash('success', 'Catégorie ajoutée avec succes');
            return $this->redirectToRoute('gestion_categories');
        }
        return $this->render('admin/newCategory.html.twig', [
            'newCategoryForm'=>$form->createView()
            ]);
 }

 // Gestion actions // afficher toutes les catégories
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
}
