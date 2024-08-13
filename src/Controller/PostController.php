<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{

    // Page d'accueil du forum avec tous les posts
    #[Route('/forum', name: 'app_forum')]
    public function index(PostRepository $postRepository): Response
    {
        // Récupère tous les posts depuis le repository
        $posts = $postRepository->findAll();
        return $this->render('post/index.html.twig', [
            'posts' => $posts,
        ]);
    }


    //creation d'un nouveau post
    #[Route('/new-post', name: 'new_post')]
    public function newPost(Request $request, PostRepository $postRepository): Response
    {
        if (!$this->getUser()) {
            // Redirigez vers la page de connexion si l'utilisateur n'est pas connecté
            return $this->redirectToRoute('app_login');
        }
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
            // s'il y a upload: $imageFile est un objet de la class UploadedFile
            // s'il n'y a pas d'upload: $imageFile est null

            //  je pars du principe par rapport au projet que l'image est facultative

            if ($imageFile) {
                //1ere étape: definir le nom du fichier
                $nomImage = date('YmdHis') . '-' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                //2eme étape : enregistrer le fichier dans le projet
                $imageFile->move(
                    $this->getParameter('post'), //l'emplacement->dans config/services.yaml à l'option parametres
                    $nomImage
                );
                //3eme étape: faut enregistrer le nom du fichier dans l'objet
                $post->setImage($nomImage);
            }
            $post->setDatePublication(new \DateTimeImmutable('now'));
            $post->setAuteur($this->getUser());
            $postRepository->add($post, true); //save c'est add dans autres versions de sf
            return $this->redirectToRoute('app_forum');
        }
        return $this->render('post/newPost.html.twig', [
            'postForm' => $form->createView()
        ]);
    }

    // Voir un post spécifique
     
    #[Route('/post/{id}', name: 'show_post')]

public function showPost(int $id, PostRepository $postRepository): Response
{
    $post = $postRepository->findPostById($id);

    return $this->render('post/showPost.html.twig', [
        'post' => $post
    ]);
}

//Modifier un post
#[Route('/post/{id}/update', name: 'update_post')]

public function updatePost(int $id,Request $request, PostRepository $postRepository): Response
{
    $post = $postRepository->find($id);
    // Création du formulaire lié à l'objet $post
    $form = $this->createForm(PostType::class, $post);

    // Traitement du formulaire s'il a été soumis
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid())
     {
        $imageFile = $form->get('image')->getData();
        if ($imageFile) {
            $nomImage = date('YmdHis') . '-' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            //2eme étape : enregistrer le fichier dans le projet
            $imageFile->move(
                $this->getParameter('post'), 
                $nomImage
            );

            //verification si le post a déja une img
            if ($post->getImage())
            {
                unlink($this->getParameter('post') . '/' . $post->getImage() );
            }

                $post->setImage($nomImage);
        }

        // Enregistrement du post modifié
        $postRepository->add($post, true);

        return $this->redirectToRoute('app_forum');
    }

    return $this->render('post/updatePost.html.twig', [
        'formUpdatePost' => $form->createView(),
        'post' => $post
    ]);
}

//Supprimer un post
#[Route('/deletePost/{id}', name: 'delete_post', methods: ['POST'])]

public function deletePost(int $id, PostRepository $postRepository, Request $request): Response
{
    $post = $postRepository->find($id);     // Récupération du post à supprimer
    // Vérification de la validité du token envoyé avec la requête

    if ($this->isCsrfTokenValid(
        'delete' . $post->getId(), //s'assurer que le token est spécifique à l'action de suppression de ce post particulier
        $request->request->get('_token')
    )) {
        if($post->getImage())
        {
            unlink($this->getParameter('post') . '/' . $post->getImage() );
        }
        $postRepository->remove($post, true);
        //$this->addFlash('success', 'le produit a bien été supprimé');
    } else {
        //$this->addFlash('error', 'le produit ne peut pas etre supprimé');
    }
    return $this->redirectToRoute('app_forum');
}
}
