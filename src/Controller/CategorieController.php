<?php

namespace App\Controller;

use App\Entity\Categorie;
use App\Form\CategorieType;
use App\Repository\CategorieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategorieController extends AbstractController
{
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
        return $this->render('admin/categories/newCategory.html.twig', [
            'newCategoryForm'=>$form->createView()
            ]);
 }
    
 // Modifier une catégorie
 #[Route ('/admin/update-category/{id}', name:'admin_update_category')]
 public function updateCategory(int $id, Request $request, CategorieRepository $catRepo): Response
 {
    if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }
        // Récupération de la catégorie
        $category = $catRepo->find($id);
        if (!$category) {
            $this->addFlash('error', 'Catégorie non trouvée');
            return $this->redirectToRoute('gestion_categories');
        }
        $form = $this->createForm(CategorieType::class, $category);
        $form->handleRequest($request);
        if($form->isSubmitted()&& $form->isValid()){
            $catRepo->add($category, true);
            $this->addFlash('success', 'Catégorie modifiée avec succes');
            return $this->redirectToRoute('gestion_categories');
        }
        return $this->render('admin/categories/updateCategory.html.twig', [
            'updateCategoryForm'=>$form->createView(),
            'category'=>$category
            ]);
 
}

// supprimer une catégorie
#[Route ('/admin/delete-category/{id}', name:'admin_delete_category')]
 public function deleteCategory(int $id, Request $request, CategorieRepository $catRepo): Response
 {
    if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_login');
        }
        // Récupération de la catégorie
        $category = $catRepo->find($id);
        if (!$category) {
            $this->addFlash('error', 'Catégorie non trouvée');
            return $this->redirectToRoute('gestion_categories');
        }
        if ($this->isCsrfTokenValid(
        'delete' . $category->getId(), 
        $request->request->get('_token')
    )) {
       if($category->getAnnonces())
       {
        $this->addFlash('error', 'Impossible de supprimer une catégorie avec des annonces associées');
        return $this->redirectToRoute('gestion_categories');
    }
        
    else {
        $catRepo->remove($category, true);
        $this->addFlash('success', 'Catégorie supprimée avec succés');
    }
    return $this->redirectToRoute('gestion_categories');
}
}
}
