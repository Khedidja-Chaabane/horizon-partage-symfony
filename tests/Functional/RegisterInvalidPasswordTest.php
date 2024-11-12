<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterInvalidPasswordTest extends WebTestCase
{

    public function testRegisterInvalidPassword(): void
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
        $form['registration_form[nomUser]'] = 'Royal';
        $form['registration_form[prenomUser]'] = 'Marie';
        $form['registration_form[userName]'] = 'MarieR';
        $form['registration_form[email]'] = 'marie.royal@gmail.com'; 
        $form['registration_form[plainPassword]'] = '123456'; // Mot de passe ne respectant pas les critères

        // Soumettre le formulaire
        $client->submit($form);
        // Vérifier que la page n'a pas redirigé (inscription échouée)
        $this->assertResponseStatusCodeSame(200);
        // Vérifier les messages d'erreur spécifiques sur la page
        $this->assertSelectorTextContains('.invalid-feedback', 'Votre mot de passe doit contenir au moins 12 caractères, incluant au moins une majuscule, une minuscule, un chiffre et un caractère spécial.');

        // Vérifier que l'utilisateur n'a pas été créé dans la base de données
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('marie.royal@gmail.com');
        $this->assertNull($user);
    }
}
