<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
public function register(Request $request, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
{
    $user = new User();
    $form = $this->createForm(RegistrationFormType::class, $user);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        // Encode le mot de passe
        $user->setPassword(
            $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData()  // Mot de passe en clair récupéré depuis le formulaire
            )
        );
        
        // Gestion du téléchargement de la photo de profil
        $imageFile = $form->get('photoProfile')->getData();
        if ($imageFile) {
            // Définir le nom du fichier
            $nomImage = date('YmdHis') . '-' . uniqid() . '.' . $imageFile->getClientOriginalExtension();
            // Enregistrer le fichier dans le projet
            $imageFile->move(
                $this->getParameter('user'), // l'emplacement défini dans config/services.yaml
                $nomImage
            );
            // Enregistrer le nom du fichier dans l'objet
            $user->setPhotoProfile($nomImage);
        } else {
            // Si aucune image n'est téléchargée, définir l'image par défaut
            $user->setPhotoProfile('default-profile-img.png');
        }

        // Assigner le rôle par défaut 'ROLE_USER'
        $user->setRoles(['ROLE_USER']);
        
        // Sauvegarder l'utilisateur en utilisant la méthode 'save' du repository
        $userRepository->save($user, true); // 'true' pour flusher les changements

        $this->addFlash('success', 'Inscription réussie, veuillez vous connecter !');

        // Redirection après l'enregistrement
        return $this->redirectToRoute('app_login');
    }

    return $this->render('user/register.html.twig', [
        'registrationForm' => $form->createView(),
    ]);
}

}