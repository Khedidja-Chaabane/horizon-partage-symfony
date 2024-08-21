<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
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
            
            $imageFile = $form->get('photoProfile')->getData();
            // s'il y a upload: $imageFile est un objet de la classe UploadedFile
            // s'il n'y a pas d'upload: $imageFile est null

            // Gestion du téléchargement de la photo de profil
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
            } else {
                // Si aucune image n'est téléchargée, définir une image par défaut
                $user->setPhotoProfile('default-profile-img.png');
            }
            
            // Assigner le rôle par défaut 'ROLE_USER'
            $user->setRoles(['ROLE_USER']);
            // Sauvegarder l'utilisateur
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('success', 'Inscription réussie, veuillez vous connecter !');

            // Redirection après l'enregistrement
            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
