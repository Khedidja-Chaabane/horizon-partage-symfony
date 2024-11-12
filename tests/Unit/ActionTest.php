namespace App\Tests\Unit;

use App\Entity\Action;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class ActionTest extends TestCase
{
    public function testActionEntity(): void
    {
        // Création d'une instance de l'entité Action
        $action = new Action();

        // Test du setter et du getter pour le titre
        $action->setTitre('Aide alimentaire');
        $this->assertSame('Aide alimentaire', $action->getTitre());

        // Test du setter et du getter pour la description
        $action->setDescription('Action pour aider les personnes en difficulté');
        $this->assertSame('Action pour aider les personnes en difficulté', $action->getDescription());

        // Test du setter et du getter pour l'image
        $action->setImage('image.png');
        $this->assertSame('image.png', $action->getImage());

        // Test du setter et du getter pour le nombre de places
        $action->setNombrePlaces(50);
        $this->assertSame(50, $action->getNombrePlaces());

        // Test du setter et du getter pour la date de début
        $dateDebut = new \DateTime('2024-12-01');
        $action->setDateDebut($dateDebut);
        $this->assertSame($dateDebut, $action->getDateDebut());

        // Test du setter et du getter pour la date de fin
        $dateFin = new \DateTime('2024-12-10');
        $action->setDateFin($dateFin);
        $this->assertSame($dateFin, $action->getDateFin());

        // Test du setter et du getter pour le lieu
        $action->setLieu('Paris');
        $this->assertSame('Paris', $action->getLieu());

        // Test de la collection des inscrits
        $user = new User();
        $action->addInscrit($user);
        $this->assertTrue($action->getInscrit()->contains($user));
        
        // Test de suppression d'un inscrit
        $action->removeInscrit($user);
        $this->assertFalse($action->getInscrit()->contains($user));
    }
}
