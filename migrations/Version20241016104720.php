<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241016104720 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action ADD date_fin DATE DEFAULT NULL, ADD horaire_fin TIME DEFAULT NULL, DROP tarif, CHANGE date date_debut DATE DEFAULT NULL, CHANGE horaire horaire_debut TIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action ADD tarif DOUBLE PRECISION DEFAULT NULL, ADD date DATE DEFAULT NULL, ADD horaire TIME DEFAULT NULL, DROP date_debut, DROP date_fin, DROP horaire_debut, DROP horaire_fin');
    }
}
