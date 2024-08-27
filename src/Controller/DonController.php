<?php

namespace App\Controller;

use App\Form\DonType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class DonController extends AbstractController
{
    #[Route('/don', name: 'dons')]
    public function index(Request $request, SessionInterface $session): Response
    {
        $form = $this->createForm(DonType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer les données du formulaire
            $data = $form->getData();

            // Vérifier si un montant personnalisé est saisi, sinon utiliser le montant sélectionné
            $montant = $data['montant_personnalise'] ?: $data['montant'];

            if ($montant > 0) {
                                // Récupère la contribution actuelle de la session ou initialise un tableau vide

            $contribution = $session->get('contribution', []);
                            // Ajoute le montant du don à la liste des contributions

            $contribution[] = $montant;
                            // Met à jour la session avec la nouvelle liste de contributions

            $session->set('contribution', $contribution);

            return $this->redirectToRoute('contribution');
        }
        }

        return $this->render('don/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/contribution', name: 'contribution')]
    public function contribution(SessionInterface $session): Response
    {
        $contribution = $session->get('contribution', []);
        // Calculer le total des dons
        $total = array_sum($contribution);
        return $this->render('don/contribution.html.twig', [
            'contribution' => $contribution,
            'total' => $total,
        ]);
    }

    #[Route('/contribution/augmenter/{index}', name: 'augmenter_don')]
    public function augmenterDon(SessionInterface $session, int $index): Response
    {
        //récupère le tableau de la contribution stocké dans la session
        $contribution = $session->get('contribution', []);
        // Vérifie que l'index est valide et que la contribution existe à cet index
        if (isset($contribution[$index])) {
            $contribution[$index] += 1; // augmenter le montant de 1
        }
        // Met à jour la session avec la nouvelle contribution

        $session->set('contribution', $contribution);
        return $this->redirectToRoute('contribution');
    }

    #[Route('/contribution/diminuer/{index}', name: 'diminuer_don')]
    public function diminuerDon(SessionInterface $session, int $index): Response
    {
        $contribution = $session->get('contribution', []);
        if (isset($contribution[$index]) && $contribution[$index] > 1) {
            $contribution[$index] -= 1; // Diminue le montant de 1
        }
        $session->set('contribution', $contribution);

        return $this->redirectToRoute('contribution');
    }

    #[Route('/contribution/supprimer/{index}', name: 'supprimer_don')]
    public function supprimerDon(SessionInterface $session, int $index): Response
    {
        // Récupère le tableau des contributions depuis la session. Si aucune contribution n'existe, un tableau vide est retourné.

        $contribution = $session->get('contribution', []);
        // Vérifie si l'index spécifié existe dans le tableau des contributions et n'est pas vide.

        if (!empty($contribution[$index])) {
            // Supprime l'élément du tableau de contributions à l'index spécifié.

            unset($contribution[$index]);
        }
        // Met à jour la session avec le tableau de contributions modifié.

        $session->set('contribution', $contribution);

        return $this->redirectToRoute('contribution');
    }

    #[Route('/contribution/vider', name: 'vider_contribution')]
    public function viderContribution(SessionInterface $session): Response
    {
        $session->set('contribution', []); // Vide tout le panier contribution
        $this->addFlash('success', 'Suppression réussie');
        return $this->redirectToRoute('contribution');
    }
}
