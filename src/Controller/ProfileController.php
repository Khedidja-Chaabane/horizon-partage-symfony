<?php

namespace App\Controller;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UpdateProfileType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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

    // Méthode pour changer le mot de passe
    #[Route('/profile/change-password', name: 'user_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository, SessionInterface $session): Response
    {
        // Vérification que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        // Création du formulaire de changement de mot de passe
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération des données du formulaire
            $currentPassword = $form->get('password')->getData();
            $newPassword = $form->get('newPassword')->getData();

            // Vérification du mot de passe actuel
            if (!$passwordHasher->isPasswordValid($user, $currentPassword)) {
                // Si le mot de passe actuel est incorrect, on ajoute un message flash d'erreur
                $this->addFlash('error', 'Le mot de passe actuel est incorrect.');
                return $this->redirectToRoute('user_change_password');
            }

            // Hachage et mise à jour du nouveau mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $newPassword);
            $user->setPassword($hashedPassword);
            $userRepository->save($user, true);

            // Ajout d'un message flash de succès et redirection
            $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');
            return $this->redirectToRoute('app_profile');
        }

        // Affichage du formulaire
        return $this->render('profile/changePassword.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Méthode pour supprimer son profil
    #[Route('/profile/delete/{id}', name: 'delete_profile')]
    public function deleteProfile(Request $request, UserRepository $userRepository, SessionInterface $session): Response
    {
        // Vérification que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        if ($this->isCsrfTokenValid(
        'delete' . $user->getId(), //s'assurer que le token est spécifique à l'action de suppression de cet utilisateur en particulier
        $request->request->get('_token')
    )) {
        if($user->getPhotoProfile())
        {
            unlink($this->getParameter('user') . '/' . $user->getPhotoProfile());
        }
        $userRepository->remove($user, true);
        $this->addFlash('success', 'Utilisateur supprimé avec succés');
    } else {
        $this->addFlash('error', 'L\'utilisateur n\'a pas pu etre supprimé');
    }
    return $this->redirectToRoute('app_home');
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

    // Récupérer les dons de l'utilisateur sur son profil
    #[Route('/profile/donations', name: 'profile_donations')]
    public function userDonations(): Response
    {
        // Vérification que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        // Récupérer les dons de l'utilisateur via la méthode getDonations()
        $dons = $user->getDons();
        // Affichage de la page et Passage de l'utilisateur à la vue Twig sous la variable user
        return $this->render('profile/userDonations.html.twig', [
            'user' => $user,
            'dons' => $dons,
        ]);
    }

     // Route pour afficher les messages dans le profil
     #[Route('/profile/messages', name: 'app_profile_messages')]
    public function userMessages(): Response
    {
          // Vérification que l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
  // Récupérer les messages de l'utilisateur 
  $posts = $user->getPosts();
        // Affichage de la page et Passage de l'utilisateur à la vue Twig sous la variable user
        return $this->render('profile/userPosts.html.twig', [
            'user' => $user,
        'posts' => $posts,
        ]);
    }

}
