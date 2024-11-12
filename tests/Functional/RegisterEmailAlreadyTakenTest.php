<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterEmailAlreadyTakenTest extends WebTestCase
{

    public function testEmailAlreadyTaken(): void
    {
        // Créer un client HTTP pour simuler des requêtes
        $client = static::createClient();

        // Accéder à la page d'inscription
        $crawler = $client->request('GET', '/register');

        // Vérifier que la page d'inscription se charge bien
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Inscription'); // Vérifier le titre de la page

        // Remplir le formulaire d'inscription
        $form = $crawler->selectButton('S\'inscrire')->form(); // Le bouton du formulaire d'inscription
        $form['registration_form[nomUser]'] = 'Dupont';
        $form['registration_form[prenomUser]'] = 'Jean Jacques';
        $form['registration_form[userName]'] = 'Jean78';
        $form['registration_form[email]'] = 'jean.dupont@gmail.com'; //email déja pris
        $form['registration_form[plainPassword]'] = 'Azerty123123@'; // Mot de passe respectant les critères

        // Soumettre le formulaire
        $client->submit($form);
        // Vérifier que la page n'a pas redirigé (inscription échouée)
        $this->assertResponseStatusCodeSame(200);
        // Vérifier les messages d'erreur spécifiques sur la page
        $this->assertSelectorTextContains('.invalid-feedback', 'Un compte existe déja avec cette adresse mail');
    }
}
