<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210121114416 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE nczi_morning_email (id INT UNSIGNED NOT NULL, published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', is_manually_overridden TINYINT(1) NOT NULL, reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', slovakia_tests_pcr_positive_delta INT DEFAULT NULL, slovakia_tests_pcr_positive_delta_without_quarantine INT DEFAULT NULL, region_ba_tests_pcr_positive_total INT DEFAULT NULL, region_bb_tests_pcr_positive_total INT DEFAULT NULL, region_ke_tests_pcr_positive_total INT DEFAULT NULL, region_nr_tests_pcr_positive_total INT DEFAULT NULL, region_po_tests_pcr_positive_total INT DEFAULT NULL, region_tn_tests_pcr_positive_total INT DEFAULT NULL, region_tt_tests_pcr_positive_total INT DEFAULT NULL, region_za_tests_pcr_positive_total INT DEFAULT NULL, region_ba_tests_pcr_positive_delta INT DEFAULT NULL, region_bb_tests_pcr_positive_delta INT DEFAULT NULL, region_ke_tests_pcr_positive_delta INT DEFAULT NULL, region_nr_tests_pcr_positive_delta INT DEFAULT NULL, region_po_tests_pcr_positive_delta INT DEFAULT NULL, region_tn_tests_pcr_positive_delta INT DEFAULT NULL, region_tt_tests_pcr_positive_delta INT DEFAULT NULL, region_za_tests_pcr_positive_delta INT DEFAULT NULL, slovakia_tests_ag_all_total INT DEFAULT NULL, slovakia_tests_ag_all_delta INT DEFAULT NULL, slovakia_tests_ag_positive_total INT DEFAULT NULL, slovakia_tests_ag_positive_delta INT DEFAULT NULL, hospital_beds_occupied_jis_covid INT DEFAULT NULL, hospital_patients_confirmed_covid INT DEFAULT NULL, hospital_patients_suspected_covid INT DEFAULT NULL, hospital_patients_ventilated_covid INT DEFAULT NULL, slovakia_vaccination_all_total INT DEFAULT NULL, slovakia_vaccination_all_delta INT DEFAULT NULL, hospital_patients_all_covid INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_A256D3243625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE region ADD abbreviation VARCHAR(2) NOT NULL');
        $this->addSql('UPDATE region SET abbreviation=id');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F62F176BCF3411D ON region (abbreviation)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE nczi_morning_email');
        $this->addSql('DROP INDEX UNIQ_F62F176BCF3411D ON region');
        $this->addSql('ALTER TABLE region DROP abbreviation');
    }
}
