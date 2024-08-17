<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240817171234 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action ADD categorie_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE action ADD CONSTRAINT FK_47CC8C92BCF5E72D FOREIGN KEY (categorie_id) REFERENCES categorie (id)');
        $this->addSql('CREATE INDEX IDX_47CC8C92BCF5E72D ON action (categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action DROP FOREIGN KEY FK_47CC8C92BCF5E72D');
        $this->addSql('DROP INDEX IDX_47CC8C92BCF5E72D ON action');
        $this->addSql('ALTER TABLE action DROP categorie_id');
    }
}
