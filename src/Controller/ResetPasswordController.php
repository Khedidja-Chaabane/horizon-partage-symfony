<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use App\Form\ResetPasswordRequestFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\ResetPassword\Controller\ResetPasswordControllerTrait;
use SymfonyCasts\Bundle\ResetPassword\Exception\ResetPasswordExceptionInterface;
use SymfonyCasts\Bundle\ResetPassword\ResetPasswordHelperInterface;

#[Route('/reset-password')]
class ResetPasswordController extends AbstractController
{
    use ResetPasswordControllerTrait; // Utilisation du trait pour des fonctionnalités communes de réinitialisation
    // Constructeur pour injecter les services nécessaires dans le contrôleur
    public function __construct(
        private ResetPasswordHelperInterface $resetPasswordHelper, // Helper pour gérer la logique de réinitialisation
        private EntityManagerInterface $entityManager // Gestionnaire d'entités pour la base de données
    ) {}

    /**
     * Affiche et traite le formulaire pour demander une réinitialisation de mot de passe.
     */
    #[Route('', name: 'app_forgot_password_request')]
    public function request(Request $request, MailerInterface $mailer, TranslatorInterface $translator): Response
    {
        $form = $this->createForm(ResetPasswordRequestFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Traitement de l'envoi de l'email de réinitialisation de mot de passe
            return $this->processSendingPasswordResetEmail(
                $form->get('email')->getData(),
                $mailer, // Service d'envoi d'emails
                $translator // Service de traduction
            );
        }

        return $this->render('reset_password/request.html.twig', [
            'requestForm' => $form->createView(),
        ]);
    }

    /**
     * Page de confirmation après qu'un utilisateur a demandé une réinitialisation de mot de passe.
     */
    #[Route('/check-email', name: 'app_check_email')]
    public function checkEmail(): Response
    {
        // Génère un faux token si l'utilisateur n'existe pas ou si quelqu'un accède à cette page directement.
        // Cela évite de révéler si un utilisateur a été trouvé avec l'adresse email donnée ou non
        if (null === ($resetToken = $this->getTokenObjectFromSession())) {
            $resetToken = $this->resetPasswordHelper->generateFakeResetToken(); // Génération d'un faux jeton de réinitialisation
        }
        // Affichage de la vue avec le token de réinitialisation
        return $this->render('reset_password/check_email.html.twig', [
            'resetToken' => $resetToken,
        ]);
    }

    /**
     * Valide et traite l'URL de réinitialisation que l'utilisateur a cliquée dans son email.
     */
    #[Route('/reset/{token}', name: 'app_reset_password')]
    public function reset(Request $request, UserPasswordHasherInterface $passwordHasher, TranslatorInterface $translator, string $token = null): Response
    {
        // Si un token est fourni dans l'URL, on le stocke dans la session et on redirige pour éviter les fuites via l'URL

        if ($token) {

            $this->storeTokenInSession($token);

            return $this->redirectToRoute('app_reset_password');
        }
        // Récupération du token depuis la session
        $token = $this->getTokenFromSession();
        if (null === $token) {
            // Erreur si pas de token
            throw $this->createNotFoundException('Aucun jeton de réinitialisation de mot de passe trouvé dans l\'URL ou la session.');
        }

        try {
            // Validation du token et récupération de l'utilisateur correspondant
            $user = $this->resetPasswordHelper->validateTokenAndFetchUser($token);
        } catch (ResetPasswordExceptionInterface $e) {
            // En cas d'erreur de validation du token, on affiche un message d'erreur
            $this->addFlash('reset_password_error', sprintf(
                '%s - %s',
                $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_VALIDATE, [], 'ResetPasswordBundle'),
                $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            ));
            // Redirection vers la demande de réinitialisation
            return $this->redirectToRoute('app_forgot_password_request');
        }

        // Le token est valide, on permet à l'utilisateur de changer son mot de passe
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Le token de réinitialisation ne doit être utilisé qu'une seule fois, on le supprime
            $this->resetPasswordHelper->removeResetRequest($token);

            // Hachage et encodage du nouveau mot de passe, puis affectation à l'utilisateur
            $encodedPassword = $passwordHasher->hashPassword(
                $user,
                $form->get('plainPassword')->getData() // Récupération du mot de passe en clair depuis le formulaire
            );

            $user->setPassword($encodedPassword); // Mise à jour du mot de passe de l'utilisateur
            $this->entityManager->flush(); // Enregistrement des modifications dans la base de données

            // Nettoyage de la session après le changement de mot de passe
            $this->cleanSessionAfterReset();

            return $this->redirectToRoute('app_home'); // Redirection vers la page d'accueil
        }

        return $this->render('reset_password/reset.html.twig', [
            'resetForm' => $form->createView(),
        ]);
    }

    // Fonction privée pour traiter l'envoi de l'email de réinitialisation de mot de passe
    private function processSendingPasswordResetEmail(string $emailFormData, MailerInterface $mailer, TranslatorInterface $translator): RedirectResponse
    {
        // Rechercher l'utilisateur par son adresse email
        $user = $this->entityManager->getRepository(User::class)->findOneBy([
            'email' => $emailFormData,
        ]);

        // Ne pas révéler si un compte utilisateur a été trouvé ou non
        if (!$user) {
            return $this->redirectToRoute('app_check_email'); // Redirection vers la page de vérification d'email
        }

        try {
            // Génération du token de réinitialisation pour l'utilisateur
            $resetToken = $this->resetPasswordHelper->generateResetToken($user);
        } catch (ResetPasswordExceptionInterface $e) {
            // Si une exception est levée, redirige sans afficher d'erreur spécifique pour des raisons de sécurité
            // If you want to tell the user why a reset email was not sent, uncomment
            // the lines below and change the redirect to 'app_forgot_password_request'.
            // Caution: This may reveal if a user is registered or not.
            //
            // $this->addFlash('reset_password_error', sprintf(
            //     '%s - %s',
            //     $translator->trans(ResetPasswordExceptionInterface::MESSAGE_PROBLEM_HANDLE, [], 'ResetPasswordBundle'),
            //     $translator->trans($e->getReason(), [], 'ResetPasswordBundle')
            // ));

            return $this->redirectToRoute('app_check_email');
        }
        // Création de l'email avec le token de réinitialisation
        $email = (new TemplatedEmail())
            ->from(new Address('contact@horizonpartage.org', 'horizonpartage')) // Adresse de l'expéditeur
            ->to($user->getEmail()) // Adresse du destinataire
            ->subject('Your password reset request') // Sujet de l'email
            ->htmlTemplate('reset_password/email.html.twig') // Template Twig pour le corps de l'email
            ->context([
                'resetToken' => $resetToken, // Passage du token de réinitialisation au template
            ]);

        $mailer->send($email); // Envoi de l'email

        // Stockage du token dans la session pour récupération dans la route de vérification d'email
        $this->setTokenObjectInSession($resetToken);

        return $this->redirectToRoute('app_check_email');// Redirection vers la page de vérification d'email
    }
}
