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
    #[Route('/categorie', name: 'app_categorie')]
    public function index(): Response
    {
        return $this->render('categorie/index.html.twig', [
            'controller_name' => 'CategorieController',
        ]);
    }

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
    
}
