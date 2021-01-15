<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210115185404 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE city (id INT AUTO_INCREMENT NOT NULL, district_id INT NOT NULL, code VARCHAR(50) NOT NULL, title VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_2D5B023477153098 (code), INDEX IDX_2D5B0234B08FA272 (district_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE district (id INT AUTO_INCREMENT NOT NULL, region_id INT NOT NULL, title VARCHAR(255) NOT NULL, code VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_31C1548777153098 (code), INDEX IDX_31C1548798260155 (region_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE district_hospital_beds (id INT UNSIGNED NOT NULL, district_id INT DEFAULT NULL, oldest_reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', newest_reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', capacity_all INT DEFAULT NULL, free_all INT DEFAULT NULL, capacity_covid INT DEFAULT NULL, occupied_jis_covid INT DEFAULT NULL, occupied_oaim_covid INT DEFAULT NULL, occupied_o2_covid INT DEFAULT NULL, occupied_other_covid INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_15339573B08FA272 (district_id), INDEX IDX_1533957343625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE district_hospital_patients (id INT UNSIGNED NOT NULL, district_id INT DEFAULT NULL, oldest_reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', newest_reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', ventilated_covid INT DEFAULT NULL, non_covid INT DEFAULT NULL, confirmed_covid INT DEFAULT NULL, suspected_covid INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1519C93CB08FA272 (district_id), INDEX IDX_1519C93C43625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hospital (id INT AUTO_INCREMENT NOT NULL, city_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, code VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_4282C85B77153098 (code), INDEX IDX_4282C85B8BAC62AF (city_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hospital_beds (id INT UNSIGNED NOT NULL, hospital_id INT DEFAULT NULL, reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', capacity_all INT DEFAULT NULL, free_all INT DEFAULT NULL, capacity_covid INT DEFAULT NULL, occupied_jis_covid INT DEFAULT NULL, occupied_oaim_covid INT DEFAULT NULL, occupied_o2_covid INT DEFAULT NULL, occupied_other_covid INT DEFAULT NULL, INDEX IDX_B70BCD3B63DBB69 (hospital_id), INDEX IDX_B70BCD3B43625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE hospital_patients (id INT UNSIGNED NOT NULL, hospital_id INT DEFAULT NULL, reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ventilated_covid INT DEFAULT NULL, non_covid INT DEFAULT NULL, confirmed_covid INT DEFAULT NULL, suspected_covid INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_651B8D963DBB69 (hospital_id), INDEX IDX_651B8D943625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, code VARCHAR(50) NOT NULL, UNIQUE INDEX UNIQ_F62F17677153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region_hospital_beds (id INT UNSIGNED NOT NULL, region_id INT DEFAULT NULL, oldest_reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', newest_reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', capacity_all INT DEFAULT NULL, free_all INT DEFAULT NULL, capacity_covid INT DEFAULT NULL, occupied_jis_covid INT DEFAULT NULL, occupied_oaim_covid INT DEFAULT NULL, occupied_o2_covid INT DEFAULT NULL, occupied_other_covid INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_9A6FF3A198260155 (region_id), INDEX IDX_9A6FF3A143625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region_hospital_patients (id INT UNSIGNED NOT NULL, region_id INT DEFAULT NULL, oldest_reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', newest_reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', ventilated_covid INT DEFAULT NULL, non_covid INT DEFAULT NULL, confirmed_covid INT DEFAULT NULL, suspected_covid INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BE17835598260155 (region_id), INDEX IDX_BE17835543625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slovakia_hospital_beds (id INT UNSIGNED NOT NULL, oldest_reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', newest_reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', capacity_all INT DEFAULT NULL, free_all INT DEFAULT NULL, capacity_covid INT DEFAULT NULL, occupied_jis_covid INT DEFAULT NULL, occupied_oaim_covid INT DEFAULT NULL, occupied_o2_covid INT DEFAULT NULL, occupied_other_covid INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_80357C3D43625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slovakia_hospital_patients (id INT UNSIGNED NOT NULL, oldest_reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', newest_reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', ventilated_covid INT DEFAULT NULL, non_covid INT DEFAULT NULL, confirmed_covid INT DEFAULT NULL, suspected_covid INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1EDECD5043625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE city ADD CONSTRAINT FK_2D5B0234B08FA272 FOREIGN KEY (district_id) REFERENCES district (id)');
        $this->addSql('ALTER TABLE district ADD CONSTRAINT FK_31C1548798260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE district_hospital_beds ADD CONSTRAINT FK_15339573B08FA272 FOREIGN KEY (district_id) REFERENCES district (id)');
        $this->addSql('ALTER TABLE district_hospital_patients ADD CONSTRAINT FK_1519C93CB08FA272 FOREIGN KEY (district_id) REFERENCES district (id)');
        $this->addSql('ALTER TABLE hospital ADD CONSTRAINT FK_4282C85B8BAC62AF FOREIGN KEY (city_id) REFERENCES city (id)');
        $this->addSql('ALTER TABLE hospital_beds ADD CONSTRAINT FK_B70BCD3B63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE hospital_patients ADD CONSTRAINT FK_651B8D963DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE region_hospital_beds ADD CONSTRAINT FK_9A6FF3A198260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE region_hospital_patients ADD CONSTRAINT FK_BE17835598260155 FOREIGN KEY (region_id) REFERENCES region (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hospital DROP FOREIGN KEY FK_4282C85B8BAC62AF');
        $this->addSql('ALTER TABLE city DROP FOREIGN KEY FK_2D5B0234B08FA272');
        $this->addSql('ALTER TABLE district_hospital_beds DROP FOREIGN KEY FK_15339573B08FA272');
        $this->addSql('ALTER TABLE district_hospital_patients DROP FOREIGN KEY FK_1519C93CB08FA272');
        $this->addSql('ALTER TABLE hospital_beds DROP FOREIGN KEY FK_B70BCD3B63DBB69');
        $this->addSql('ALTER TABLE hospital_patients DROP FOREIGN KEY FK_651B8D963DBB69');
        $this->addSql('ALTER TABLE district DROP FOREIGN KEY FK_31C1548798260155');
        $this->addSql('ALTER TABLE region_hospital_beds DROP FOREIGN KEY FK_9A6FF3A198260155');
        $this->addSql('ALTER TABLE region_hospital_patients DROP FOREIGN KEY FK_BE17835598260155');
        $this->addSql('DROP TABLE city');
        $this->addSql('DROP TABLE district');
        $this->addSql('DROP TABLE district_hospital_beds');
        $this->addSql('DROP TABLE district_hospital_patients');
        $this->addSql('DROP TABLE hospital');
        $this->addSql('DROP TABLE hospital_beds');
        $this->addSql('DROP TABLE hospital_patients');
        $this->addSql('DROP TABLE region');
        $this->addSql('DROP TABLE region_hospital_beds');
        $this->addSql('DROP TABLE region_hospital_patients');
        $this->addSql('DROP TABLE slovakia_hospital_beds');
        $this->addSql('DROP TABLE slovakia_hospital_patients');
    }
}
