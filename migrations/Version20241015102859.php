<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241015102859 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE action_user (action_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_FB726D479D32F035 (action_id), INDEX IDX_FB726D47A76ED395 (user_id), PRIMARY KEY(action_id, user_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE action_user ADD CONSTRAINT FK_FB726D479D32F035 FOREIGN KEY (action_id) REFERENCES action (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE action_user ADD CONSTRAINT FK_FB726D47A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE action_user DROP FOREIGN KEY FK_FB726D479D32F035');
        $this->addSql('ALTER TABLE action_user DROP FOREIGN KEY FK_FB726D47A76ED395');
        $this->addSql('DROP TABLE action_user');
    }
}
