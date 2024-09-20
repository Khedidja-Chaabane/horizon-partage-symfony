<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BasicTest extends WebTestCase
{
    public function testSomething(): void
    {
        //$client simule un navigateur, au lieu de faire des appels HTTP au serveur, il appelle directement l'app symfony
        $client = static::createClient();
        $crawler = $client->request('GET', '/home');

        $this->assertResponseIsSuccessful();
        //$this->assertSelectorTextContains('h1', 'Hello World');
    }
}
