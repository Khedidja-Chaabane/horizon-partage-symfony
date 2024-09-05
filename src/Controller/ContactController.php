<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\ContactRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
public function contact(Request $request, ContactRepository $contactRepo, Security $security): Response
{
    // Récupérer l'utilisateur connecté
    $user = $security->getUser();
    
    // Créer un nouveau contact
    $contact = new Contact();
    
    // Si l'utilisateur est connecté, préremplir le formulaire avec ses informations
    if ($user instanceof UserInterface) { ///vérifie si l'utilisateur est bien connecté avant de préremplir les champs.
        $contact->setNom($user->getNomUser());
        $contact->setPrenom($user->getPrenomUser());
        $contact->setEmail($user->getEmail());
    }
    
    // Créer le formulaire
    $form = $this->createForm(ContactType::class, $contact);
    
    $form->handleRequest($request);
    
    if($form->isSubmitted() && $form->isValid()) {
        $contact->setCreatedAt(new \DateTimeImmutable('now'));
        $contactRepo->save($contact, true);
        
        $this->addFlash('success', 'Message envoyé avec succès');
        return $this->redirectToRoute('app_home');
    }

    return $this->render('contact/contact.html.twig', [
        'form' => $form->createView(),
    ]);
}

}
