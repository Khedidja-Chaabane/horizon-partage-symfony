<?php

namespace App\Controller;

use App\Entity\Action;
use App\Form\ActionType;
use App\Form\CategorieFilterType;
use App\Repository\ActionRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActionController extends AbstractController
{
    // Page actions coté client
    #[Route('/actions', name: 'app_actions')]
    public function index(ActionRepository $actionRepo, Request $request): Response
    {
        // Créer une variable pour stocker la catégorie sélectionnée
        $categorieName = null;
        // Créer le formulaire
        $form = $this->createForm(CategorieFilterType::class);
        $form->handleRequest($request);
        // Si le formulaire est soumis et valide
        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier quel bouton a été cliqué et définir la catégorie en conséquence
            if ($form->get('cours')->isClicked()) {
                $categorieName = 'cours';
            } elseif ($form->get('atelier')->isClicked()) {
                $categorieName = 'atelier';
            } elseif ($form->get('service')->isClicked()) {
                $categorieName = 'service';
            } elseif ($form->get('aide')->isClicked()) {
                $categorieName = 'aide';
            }
        }
        // Récupérer les actions en fonction de la catégorie sélectionnée
        if ($categorieName) {
            // Récupérer les actions pour la catégorie sélectionnée
            $actions = $actionRepo->findByCategorieName($categorieName);
        } else {
            // Récupérer toutes les actions par défaut
            $actions = $actionRepo->findAll();
        }
        return $this->render('action/index.html.twig', [
            'actions' => $actions,
            'categorieFilterForm' => $form->createView(),
            'categorieName' => $categorieName,
        ]);
    }

    //----------------------------------------------------------------------------------

    // creation d'une nouvelle action uniquement par l'admin

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
                    // Si une image est téléchargée, on la traite normalement
                    $nomImage = date('YmdHis') . '-' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                    $imageFile->move($this->getParameter('action'), $nomImage);
                    $action->setImage($nomImage);
                } else {
                    // Si aucune image n'est téléchargée, on définit l'image par défaut
                    $action->setImage('default.png'); // Image par défaut
                }

                // Enregistrer l'action dans la base de données
                $actionRepo->add($action, true);
                //ajouter une notification
                $this->addFlash('success', 'l\'action a bien été ajoutée');

                //redirection 
                return $this->redirectToRoute('gestion_actions');
            }
        }

        return $this->render('admin/actions/newAction.html.twig', [
            'newActionForm' => $form->createView(),
        ]);
    }
    //-----------------------------------------------------------------------------------------------------------------------------------------
    // Affichage d'une action spécifique coté client

    #[Route('/action/{id}', name: 'show_action')]
    public function showAction(int $id, ActionRepository $actionRepo): Response
    {
        $action = $actionRepo->find($id);
        if (!$action) {
            return  $this->redirectToRoute('app_actions');
        }
        return $this->render('action/showAction.html.twig', [
            'action' => $action
        ]);
    }
    //---------------------------------------------------------------------------

    // Affichage d'une action spécifique coté admin

    #[Route('admin/action/{id}', name: 'admin_show_action')]
    public function AdminShowAction(int $id, ActionRepository $actionRepo): Response
    {
        $action = $actionRepo->find($id);
        if (!$action) {
            return  $this->redirectToRoute('gestion_actions');
        }
        return $this->render('admin/actions/showAction.html.twig', [
            'action' => $action
        ]);
    }

    //---------------------------------------------------------------------------------------------------------------------------------
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
                // Si aucune nouvelle image n'est soumise,et si l'action a déja une image , conserver l'image actuelle
                $action->setImage($action->getImage());
            }
            // Enregistrement de l'action
            $actionRepo->add($action, true);
            $this->addFlash('success', 'L\'action a bien été modifiée');

            return $this->redirectToRoute('gestion_actions');
        }

        return $this->render('admin/actions/updateAction.html.twig', [
            'formUpdateAction' => $form->createView(),
            'action' => $action,
            'imagePath' => $this->getParameter('action') . '/' . $action->getImage(), // Chemin de l'image actuelle pour l'affichage
        ]);
    }

    //---------------------------------------------------------------------------------------------------------------------------------------
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


    //Inscription à une action
    #[Route('action/{id}/user-inscription', name: 'action_user_inscription')]
    public function actionUserInscription(int $id, ActionRepository $actionRepo, UserRepository $userRepo): Response
    {
        //verifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        //verifier si l'action existe
        $action = $actionRepo->find($id);
        if (!$action) {
            return $this->redirectToRoute('app_actions');
        }
        //verifier si l'utilisateur n'est pas déja inscrit à l'action
        if ($action->getInscrit()->contains($this->getUser())) {
            $this->addFlash('error', 'Vous êtes déjà inscrit(e) à cette action');
            return $this->redirectToRoute('app_actions');
        }
        //inscrire l'utilisateur à l'action
        $action->addInscrit($this->getUser());
        $actionRepo->add($action, true);
        $this->addFlash('success', 'Vous avez bien été inscrit(e) à cette action');
        return $this->redirectToRoute('app_actions');
    }

    //Désinscription d'une action
    #[Route('action/{id}/user-desinscription', name: 'action_user_desinscription')]
    public function actionUserDesinscription(int $id, ActionRepository $actionRepo, UserRepository $userRepo): Response
    {
        //verifier si l'utilisateur est connecté
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        //verifier que l'action existe et que l'utilisateur est vraiment inscrit à cette action
        $action = $actionRepo->find($id);
        if (!$action ||!$action->getInscrit()->contains($this->getUser())) {
            return $this->redirectToRoute('app_actions');
        }
        //désinscrire l'utilisateur à l'action
        $action->removeInscrit($this->getUser());
        $actionRepo->add($action, true);
        $this->addFlash('success', 'Vous avez bien été désinscrit(e) de cette action');
        return $this->redirectToRoute('app_actions');

    }
}
