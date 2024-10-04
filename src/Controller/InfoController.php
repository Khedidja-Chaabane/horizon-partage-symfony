<?php

namespace App\Controller;

use App\Entity\Info;
use App\Form\InfoType;
use App\Repository\InfoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InfoController extends AbstractController
{
    #[Route('/infos', name: 'app_infos')]
    public function index(InfoRepository $infoRepo): Response
    {
        //Affichage des 3 dernieres infos
        $recentInfos = $infoRepo->findRecentInfos(3);
        return $this->render('info/index.html.twig', [
            'recentInfos' => $recentInfos,
        ]);
    }
//affichage de toute l'actualité
#[Route('/all-infos', name: 'all_infos')]
    public function allInfos(InfoRepository $infoRepo): Response
    {
        $infos = $infoRepo->findAll();
        return $this->render('info/allInfos.html.twig', [
            'infos' => $infos,
        ]);
    }
    //Création d'une nouvelle info uniquement par l'admin
    #[Route('/admin/new-info', name: 'admin_new_info')]
    public function newInfo(Request $request, InfoRepository $infoRepo): Response
    {
        // Vérification pour sécuriser la tâche
        if (!$this->getUser() || !$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }
        $info = new Info();
        $form = $this->createForm(InfoType::class, $info);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                $nomImage = date('YmdHis') . '-' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                $imageFile->move(
                    $this->getParameter('info'), //l'emplacement->dans config/services.yaml à l'option parametres
                    $nomImage
                );
                $info->setImage($nomImage);
            }
            $info->setCreatedAt(new \DateTimeImmutable('now'));
            $infoRepo->save($info, true);
            $this->addFlash('success', 'Actualité ajoutée avec succés');
            return $this->redirectToRoute('gestion_infos');
        }
        return $this->render('admin/infos/newInfo.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Affichage d'une info spécifique coté client

    #[Route('/info/{id}', name: 'show_info')]
    public function showInfo(int $id, InfoRepository $infoRepo): Response
    {
        $info = $infoRepo->find($id);
        if (!$info) {
            return  $this->redirectToRoute('all_infos');
        }
        return $this->render('info/showInfo.html.twig', [
            'info' => $info
        ]);
    }
///---------------------------------------------------------------------
// Affichage d'une info spécifique coté admin

#[Route('admin/info/{id}', name: 'admin_show_info')]
    public function adminShowInfo(int $id, InfoRepository $infoRepo): Response
    {
        $info = $infoRepo->find($id);
        if (!$info) {
            return  $this->redirectToRoute('all_infos');
        }
        return $this->render('admin/infos/showInfo.html.twig', [
            'info' => $info
        ]);
    }

    //---------------------------------------------------------------------------------------------------------------------------------
    // Modifier une info uniquement par l'admin
    #[Route('admin/info/{id}/update', name: 'admin_update_info')]
    public function updateInfo(int $id, Request $request, InfoRepository $infoRepo): Response
    {
        // Vérification pour sécuriser la tâche
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }

        // Récupération de l'info
        $info = $infoRepo->find($id);
        if (!$info) {
            $this->addFlash('error', 'Actualité non trouvée');
            return $this->redirectToRoute('gestion_infos');
        }

        // Création du formulaire lié à l'objet $info
        $form = $this->createForm(InfoType::class, $info);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du fichier image soumis par l'utilisateur
            $imageFile = $form->get('image')->getData();
            if ($imageFile) {
                // Si une nouvelle image est soumise
                $nomImage = date('YmdHis') . '-' . uniqid() . '.' . $imageFile->getClientOriginalExtension();

                // Enregistrement du fichier dans le répertoire des actions
                $imageFile->move($this->getParameter('info'), $nomImage);

                // Suppression de l'ancienne image
                if ($info->getImage()) {
                    unlink($this->getParameter('info') . '/' . $info->getImage());
                }

                // Mise à jour de l'image dans l'entité
                $info->setImage($nomImage);
            } else {
                // Si aucune nouvelle image n'est soumise, conserver l'image actuelle
                $info->setImage($info->getImage());
            }
            // Enregistrement de l'action
            $infoRepo->save($info, true);
            $this->addFlash('success', 'L\'actualité a bien été modifiée');

            return $this->redirectToRoute('gestion_infos');
        }

        return $this->render('admin/infos/updateInfo.html.twig', [
            'form' => $form->createView(),
            'info' => $info,
            'imagePath' => $this->getParameter('info') . '/' . $info->getImage(), // Chemin de l'image actuelle pour l'affichage
        ]);
    }

    //supprimer une info uniquement par l'admin
    #[Route('admin/info/{id}/delete', name: 'admin_delete_info', methods: ['POST'])]
    public function deleteInfo(int $id, InfoRepository $infoRepo, Request $request): Response
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }
        $info = $infoRepo->find($id);
        if (!$info) {
            return $this->redirectToRoute('gestion_infos');
        }
        if ($this->isCsrfTokenValid(
            'delete' . $info->getId(),
            $request->request->get('_token')
        )) {
            if ($info->getImage()) {
                unlink($this->getParameter('info') . '/' . $info->getImage());
            }
            $infoRepo->remove($info, true);
            $this->addFlash('success', 'l\'article a bien été supprimé');
        } else {
            $this->addFlash('error', 'l\'article ne peut pas etre supprimé');
        }
        return $this->redirectToRoute('gestion_infos');
    }
}
