<?php

namespace App\Tests\Functional;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterTest extends WebTestCase
{
    public function testRegister(): void
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
        $form['registration_form[prenomUser]'] = 'Jean';
        $form['registration_form[userName]'] = 'JeanD';
        $form['registration_form[email]'] = 'jean.dupont@gmail.com';
        $form['registration_form[plainPassword]'] = 'Motdepasse!123'; // Mot de passe respectant les critères

        // Soumettre le formulaire
        $client->submit($form);

        // Vérifier la redirection après la soumission
        $this->assertResponseRedirects('/login');

        // Suivre la redirection et vérifier le message de succès
        $client->followRedirect();
        $this->assertSelectorExists('.flash-message');
        $this->assertSelectorTextContains('.flash-message', 'Inscription réussie, veuillez vous connecter !');

        // Vérifier que l'utilisateur a bien été créé dans la base de données
        $userRepository = static::getContainer()->get(UserRepository::class);
        $user = $userRepository->findOneByEmail('jean.dupont@gmail.com');

        $this->assertNotNull($user); // L'utilisateur doit exister
        $this->assertEquals('JeanD', $user->getUserName()); // Vérifier le nom d'utilisateur

    }

   
}
