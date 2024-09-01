<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\UpdateProfileType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_profile')]
    public function index(): Response
    {
          // Vérification que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        // Affichage de la page de profil et Passage de l'utilisateur à la vue Twig sous la variable user
        return $this->render('profile/index.html.twig', [
            'user' => $user,       
        ]);
    }

 // Méthode pour modifier les données de profil
 #[Route('/profile/update/{id}', name: 'update_profile')]
    public function updateProfile(Request $request, UserRepository $userRepository, SessionInterface $session): Response
    {
        $user = $this->getUser();
        // Vérification que l'utilisateur est connecté
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        // Création du formulaire lié à l'objet $user
        $form = $this->createForm(UpdateProfileType::class, $user);

        // Traitement du formulaire s'il a été soumis
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('photoProfile')->getData();
            if ($imageFile) {
                //1ère étape: définir le nom du fichier
                $nomImage = date('YmdHis') . '-' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
                //2ème étape : enregistrer le fichier dans le projet
                $imageFile->move(
                    $this->getParameter('user'), // l'emplacement défini dans config/services.yaml à l'option paramètres
                    $nomImage
                );
                //3ème étape: enregistrer le nom du fichier dans l'objet
                $user->setPhotoProfile($nomImage);
            }
            // Enregistrement
            $userRepository->save($user, true);

            // Ajout d'une notification
        $this->addFlash('success', 'Les modifications ont bien été prises en compte.');

            // Redirection vers la page d'affichage du profil
            return $this->redirectToRoute('app_profile');
        }

        // Affichage du formulaire de modification
        return $this->render('profile/updateProfile.html.twig', [
            'formProfile' => $form->createView(),
            'user' => $user
        ]);
    }


    // Route pour afficher les posts redigés dans le profil
    #[Route('/profile/posts', name: 'app_profile_posts')]
    public function userPosts(): Response
    {
          // Vérification que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
  // Récupérer les posts de l'utilisateur via la méthode getPosts()
  $posts = $user->getPosts();
        // Affichage de la page et Passage de l'utilisateur à la vue Twig sous la variable user
        return $this->render('profile/userPosts.html.twig', [
            'user' => $user,
        'posts' => $posts,
        ]);
    }
}
