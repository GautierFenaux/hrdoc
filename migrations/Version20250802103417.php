<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250802103417 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE astreinte (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, manager_id INT NOT NULL, debut_astreinte DATETIME NOT NULL, fin_astreinte DATETIME NOT NULL, motif LONGTEXT NOT NULL, is_signature_collab TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_ok TINYINT(1) DEFAULT NULL, motif_refus_collab VARCHAR(255) DEFAULT NULL, is_ok_rh TINYINT(1) DEFAULT NULL, motif_refus_rh VARCHAR(255) DEFAULT NULL, collab_validation_date DATETIME DEFAULT NULL, rh_validation_date DATETIME DEFAULT NULL, state VARCHAR(50) NOT NULL, temps_valorise INT DEFAULT NULL, plage_horaire JSON DEFAULT NULL, temps_operation JSON DEFAULT NULL, temps_dejeuner JSON DEFAULT NULL, astreinte TINYINT(1) NOT NULL, temps_intervention_jour LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', temps_intervention_nuit LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', repos TINYINT(1) DEFAULT NULL, temps_majore LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', INDEX IDX_F23DC073A76ED395 (user_id), INDEX IDX_F23DC073783E3463 (manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE cet (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, manager_id INT DEFAULT NULL, nb_jours INT DEFAULT NULL, solde INT DEFAULT NULL, nb_jours_adebiter INT DEFAULT NULL, prise_cet_debut DATE DEFAULT NULL, prise_cet_fin DATE DEFAULT NULL, droit_conges_cumule INT DEFAULT NULL, nb_jours_conges_utilises INT DEFAULT NULL, solde_jours_conges_non_pris INT DEFAULT NULL, nb_jours_versement INT DEFAULT NULL, avis_sup_hierarchique TINYINT(1) DEFAULT NULL, commentaire_sup_hierarchique VARCHAR(255) DEFAULT NULL, avis_drh TINYINT(1) DEFAULT NULL, commentaire_drh VARCHAR(255) DEFAULT NULL, alimentation TINYINT(1) DEFAULT NULL, restitution TINYINT(1) DEFAULT NULL, utilisation TINYINT(1) DEFAULT NULL, nb_jours_liquide INT DEFAULT NULL, total_liquidation INT DEFAULT NULL, state VARCHAR(50) NOT NULL, commentaire VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', signature_profil_collab TINYINT(1) DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, INDEX IDX_FA3286ACA76ED395 (user_id), INDEX IDX_FA3286AC783E3463 (manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE manager (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, departement VARCHAR(255) DEFAULT NULL, relance_teletravail DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE retour_sur_site (id INT AUTO_INCREMENT NOT NULL, manager_id INT DEFAULT NULL, user_id INT NOT NULL, date_fin_teletravail DATETIME NOT NULL, autonomie_insuffisante TINYINT(1) DEFAULT NULL, problemes_connexion TINYINT(1) DEFAULT NULL, collaborateur_injoignable TINYINT(1) DEFAULT NULL, diminution_productivite TINYINT(1) DEFAULT NULL, desorganise_service TINYINT(1) DEFAULT NULL, autres LONGTEXT DEFAULT NULL, state VARCHAR(100) DEFAULT NULL, signature_rh TINYINT(1) DEFAULT NULL, signature_collab TINYINT(1) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', date_signature_rh DATETIME DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, entretien_rh TINYINT(1) DEFAULT NULL, INDEX IDX_A9FF0291783E3463 (manager_id), INDEX IDX_A9FF0291A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teletravail_form (id INT AUTO_INCREMENT NOT NULL, manager_id INT DEFAULT NULL, user_id INT DEFAULT NULL, nature_contrat VARCHAR(255) DEFAULT NULL, quotite VARCHAR(255) DEFAULT NULL, avis_manager TINYINT(1) DEFAULT NULL, commentaire_manager VARCHAR(255) DEFAULT NULL, avis_drh TINYINT(1) DEFAULT NULL, commentaire_drh VARCHAR(255) DEFAULT NULL, state VARCHAR(50) NOT NULL, document VARCHAR(255) DEFAULT NULL, connexion_internet TINYINT(1) NOT NULL, attestation_assurance VARCHAR(255) NOT NULL, journees_teletravaillees JSON NOT NULL, periode_essai TINYINT(1) DEFAULT NULL, activite_eligible TINYINT(1) DEFAULT NULL, autonomie_suffisante TINYINT(1) DEFAULT NULL, conditions_eligibilites TINYINT(1) DEFAULT NULL, conditions_tech_mat_adm TINYINT(1) DEFAULT NULL, desorganise_service TINYINT(1) DEFAULT NULL, a_compter_du DATETIME NOT NULL, date_fin_teletravail DATETIME NOT NULL, lieu_teletravail VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', fonction_exercee VARCHAR(255) NOT NULL, reception_demande DATETIME DEFAULT NULL, location VARCHAR(255) DEFAULT NULL, quotite_personnel VARCHAR(50) DEFAULT NULL, attestation_honneur TINYINT(1) NOT NULL, INDEX IDX_ABE9A039783E3463 (manager_id), INDEX IDX_ABE9A039A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, manager_id INT DEFAULT NULL, login VARCHAR(50) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, name VARCHAR(255) DEFAULT NULL, surname VARCHAR(255) DEFAULT NULL, metier VARCHAR(255) DEFAULT NULL, departement VARCHAR(255) DEFAULT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', eligible_tt TINYINT(1) DEFAULT NULL, actif TINYINT(1) DEFAULT NULL, eligible_cet TINYINT(1) DEFAULT NULL, first_connection TINYINT(1) DEFAULT NULL, download_kit TINYINT(1) DEFAULT NULL, matricule VARCHAR(255) DEFAULT NULL, relance_teletravail DATETIME DEFAULT NULL, forfait_heure TINYINT(1) DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649AA08CB10 (login), UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), INDEX IDX_8D93D649783E3463 (manager_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE astreinte ADD CONSTRAINT FK_F23DC073A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE astreinte ADD CONSTRAINT FK_F23DC073783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id)');
        $this->addSql('ALTER TABLE cet ADD CONSTRAINT FK_FA3286ACA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE cet ADD CONSTRAINT FK_FA3286AC783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE retour_sur_site ADD CONSTRAINT FK_A9FF0291783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id)');
        $this->addSql('ALTER TABLE retour_sur_site ADD CONSTRAINT FK_A9FF0291A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE teletravail_form ADD CONSTRAINT FK_ABE9A039783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id)');
        $this->addSql('ALTER TABLE teletravail_form ADD CONSTRAINT FK_ABE9A039A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649783E3463 FOREIGN KEY (manager_id) REFERENCES manager (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE astreinte DROP FOREIGN KEY FK_F23DC073A76ED395');
        $this->addSql('ALTER TABLE astreinte DROP FOREIGN KEY FK_F23DC073783E3463');
        $this->addSql('ALTER TABLE cet DROP FOREIGN KEY FK_FA3286ACA76ED395');
        $this->addSql('ALTER TABLE cet DROP FOREIGN KEY FK_FA3286AC783E3463');
        $this->addSql('ALTER TABLE retour_sur_site DROP FOREIGN KEY FK_A9FF0291783E3463');
        $this->addSql('ALTER TABLE retour_sur_site DROP FOREIGN KEY FK_A9FF0291A76ED395');
        $this->addSql('ALTER TABLE teletravail_form DROP FOREIGN KEY FK_ABE9A039783E3463');
        $this->addSql('ALTER TABLE teletravail_form DROP FOREIGN KEY FK_ABE9A039A76ED395');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649783E3463');
        $this->addSql('DROP TABLE astreinte');
        $this->addSql('DROP TABLE cet');
        $this->addSql('DROP TABLE manager');
        $this->addSql('DROP TABLE retour_sur_site');
        $this->addSql('DROP TABLE teletravail_form');
        $this->addSql('DROP TABLE user');
    }
}
