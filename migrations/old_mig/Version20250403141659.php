<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250403141659 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE astreinte (id INT AUTO_INCREMENT NOT NULL, debut_astreinte DATETIME NOT NULL, fin_astreinte DATETIME NOT NULL, motif LONGTEXT NOT NULL, is_signature_collab TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_ok TINYINT(1) DEFAULT NULL, motif_refus_collab VARCHAR(255) DEFAULT NULL, is_ok_rh TINYINT(1) DEFAULT NULL, motif_refus_rh VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE astreinte');
    }
}
