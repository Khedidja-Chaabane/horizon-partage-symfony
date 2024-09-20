<?php

namespace App\Tests\Unit;

use App\Entity\Post;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class PostTest extends TestCase
{
    public function testPostEntity(): void
    {
        // Création d'une instance de l'entité Post
        $post = new Post();

        // Test du setter et du getter pour le titre
        $post->setTitre('Un titre');
        $this->assertSame('Un titre', $post->getTitre());

        // Test du setter et du getter pour le texte
        $post->setTexte('Un texte');
        $this->assertSame('Un texte', $post->getTexte());

        // Test du setter et du getter pour l'image
        $post->setImage('image.jpg');
        $this->assertSame('image.jpg', $post->getImage());

        // Test du setter et du getter pour la date de publication
        $date = new \DateTimeImmutable();
        $post->setDatePublication($date);
        $this->assertSame($date, $post->getDatePublication());

        // Test du setter et du getter pour l'auteur (relation ManyToOne avec l'utilisateur)
        $user = new User();
        $post->setAuteur($user);
        $this->assertSame($user, $post->getAuteur());

        // Test de l'ID (au début, l'ID doit être null car il est généré automatiquement)
        $this->assertNull($post->getId());
    }
}
