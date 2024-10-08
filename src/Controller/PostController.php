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

    // Page d'accueil du forum 
    #[Route('/forum', name: 'app_forum')]
    public function index(PostRepository $postRepository): Response
    {
        $recentPosts = $postRepository->findRecentPosts(3); // Récupère les 3 posts les plus récents 

        return $this->render('post/index.html.twig', [
            'recentPosts' => $recentPosts,
        ]);
    }

    //Page pour afficher tout les posts
    #[Route('/allPosts', name: 'all_posts')]
    public function allPosts(PostRepository $postRepository): Response
    {
    // Récupère tous les posts depuis le repository
       $posts = $postRepository->findAll();
       return $this->render('post/allPosts.html.twig', [
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
            $postRepository->add($post, true); 
            $this->addFlash('success', 'le post a bien été ajouté');

            return $this->redirectToRoute('show_post', ['id' => $post->getId()]);
        }
        return $this->render('post/newPost.html.twig', [
            'postForm' => $form->createView()
        ]);
    }

    // Afficher un post spécifique
     
    #[Route('/post/{id}', name: 'show_post')]

public function showPost(int $id, PostRepository $postRepository): Response
{
    $post = $postRepository->findPostById($id);
    if(!$post){
            return  $this->redirectToRoute('all_posts');
        }
    return $this->render('post/showPost.html.twig', [
        'post' => $post
    ]);
}

//Modifier un post
#[Route('/post/{id}/update', name: 'update_post')]

public function updatePost(int $id,Request $request, PostRepository $postRepository): Response
{
     // Vérification que l'utilisateur est connecté
     $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
    $post = $postRepository->find($id); // chercher l'article par son id
     // Vérification que le post existe
     if (!$post) {
        // Si le post n'existe pas, rediriger vers la page all posts
        return $this->redirectToRoute('all_posts');
    }
     // Vérification que l'utilisateur est l'auteur du post
     if ($post->getAuteur() !== $user) {
        // Si l'utilisateur n'est pas l'auteur, on le redirige vers la page all posts
        return $this->redirectToRoute('all_posts');
    }
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
        $this->addFlash('success', 'le post a bien été modifié');

        return $this->redirectToRoute('show_post', ['id' => $post->getId()]);
    }

    return $this->render('post/updatePost.html.twig', [
        'postForm' => $form->createView(),
        'post' => $post
    ]);
}

//Supprimer un post
#[Route('/deletePost/{id}', name: 'delete_post', methods: ['POST'])]

public function deletePost(int $id, PostRepository $postRepository, Request $request): Response
{
     // Vérification que l'utilisateur est connecté
     $user = $this->getUser();
    if (!$user) {
        return $this->redirectToRoute('app_login');
    }

    $post = $postRepository->find($id);

    // Vérification que le post existe
    if (!$post) {
        return $this->redirectToRoute('all_posts');
    }

    // Vérification que l'utilisateur est l'auteur du post
    if ($post->getAuteur() !== $user) {
        return $this->redirectToRoute('all_posts');
    }
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
        $this->addFlash('success', 'le post a bien été supprimé');
    } else {
        $this->addFlash('error', 'le post ne peut pas etre supprimé');
    }
    return $this->redirectToRoute('all_posts');
}
}
