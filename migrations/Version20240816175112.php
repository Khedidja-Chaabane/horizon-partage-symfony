<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240816175112 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action CHANGE tarif tarif DOUBLE PRECISION DEFAULT NULL, CHANGE nombre_places nombre_places INT DEFAULT NULL, CHANGE date date DATE DEFAULT NULL, CHANGE horaire horaire TIME DEFAULT NULL, CHANGE lieu lieu VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action CHANGE tarif tarif DOUBLE PRECISION NOT NULL, CHANGE nombre_places nombre_places INT NOT NULL, CHANGE date date DATE NOT NULL, CHANGE horaire horaire TIME NOT NULL, CHANGE lieu lieu VARCHAR(255) NOT NULL');
    }
}
