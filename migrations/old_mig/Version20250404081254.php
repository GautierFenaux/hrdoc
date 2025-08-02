<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250404081254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE astreinte ADD user_id INT NOT NULL, ADD manager_id INT NOT NULL');
        $this->addSql('ALTER TABLE astreinte ADD CONSTRAINT FK_F23DC073A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE astreinte ADD CONSTRAINT FK_F23DC073783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id)');
        $this->addSql('CREATE INDEX IDX_F23DC073A76ED395 ON astreinte (user_id)');
        $this->addSql('CREATE INDEX IDX_F23DC073783E3463 ON astreinte (manager_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE astreinte DROP FOREIGN KEY FK_F23DC073A76ED395');
        $this->addSql('ALTER TABLE astreinte DROP FOREIGN KEY FK_F23DC073783E3463');
        $this->addSql('DROP INDEX IDX_F23DC073A76ED395 ON astreinte');
        $this->addSql('DROP INDEX IDX_F23DC073783E3463 ON astreinte');
        $this->addSql('ALTER TABLE astreinte DROP user_id, DROP manager_id');
    }
}
