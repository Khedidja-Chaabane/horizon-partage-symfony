<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241014111539 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D9A9C80E3');
        $this->addSql('ALTER TABLE don CHANGE donateur_id donateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D9A9C80E3 FOREIGN KEY (donateur_id) REFERENCES user (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE don DROP FOREIGN KEY FK_F8F081D9A9C80E3');
        $this->addSql('ALTER TABLE don CHANGE donateur_id donateur_id INT NOT NULL');
        $this->addSql('ALTER TABLE don ADD CONSTRAINT FK_F8F081D9A9C80E3 FOREIGN KEY (donateur_id) REFERENCES user (id)');
    }
}
