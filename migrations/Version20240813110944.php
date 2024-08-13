<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240813110944 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action ADD titre VARCHAR(255) NOT NULL, ADD description LONGTEXT NOT NULL, ADD tarif DOUBLE PRECISION NOT NULL, ADD image VARCHAR(255) NOT NULL, ADD nombre_places INT NOT NULL, ADD date DATE NOT NULL, ADD horaire TIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action DROP titre, DROP description, DROP tarif, DROP image, DROP nombre_places, DROP date, DROP horaire');
    }
}
