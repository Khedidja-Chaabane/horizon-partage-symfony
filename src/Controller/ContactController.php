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

// Affichage d'un message coté admin

#[Route('admin/message/{id}', name: 'admin_show_message')]
    public function AdminShowMessage(int $id, ContactRepository $contactRepo): Response
    {
        $contact = $contactRepo->find($id);
        if (!$contact) {
            return  $this->redirectToRoute('gestion_messages');
        }
        return $this->render('admin/contacts/showMessage.html.twig', [
            'contact' => $contact
        ]);
    }

    // Affichage d'un message coté user
    #[Route('/message/{id}', name:'show_message')]
    public function showMessage(int $id, ContactRepository $contactRepo): Response
    {
        $contact = $contactRepo->find($id);
        if (!$contact) {
            return  $this->redirectToRoute('all_messages');
        }
        return $this->render('contact/showMessage.html.twig', [
            'contact' => $contact
        ]);
    }

}
