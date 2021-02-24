<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224101456 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE raw_hospital_vaccination_substitute (id VARCHAR(255) CHARACTER SET ascii NOT NULL, hospital_id INT DEFAULT NULL, region_name VARCHAR(255) NOT NULL, city_name VARCHAR(255) NOT NULL, hospital_name VARCHAR(255) NOT NULL, link VARCHAR(255) DEFAULT NULL, note LONGTEXT DEFAULT NULL, phones LONGTEXT DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4BC462FC63DBB69 (hospital_id), INDEX IDX_4BC462FC43625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE vaccination_contacts (id INT NOT NULL, hospital_id INT DEFAULT NULL, substitutes_phones LONGTEXT DEFAULT NULL, substitutes_emails VARCHAR(255) DEFAULT NULL, substitutes_link VARCHAR(255) DEFAULT NULL, substitutes_note LONGTEXT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B7F0BC2763DBB69 (hospital_id), INDEX IDX_B7F0BC2743625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE raw_hospital_vaccination_substitute ADD CONSTRAINT FK_4BC462FC63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('ALTER TABLE vaccination_contacts ADD CONSTRAINT FK_B7F0BC2763DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raw_vaccination_substitute_hospital ADD CONSTRAINT FK_DEA54B6F63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('DROP TABLE raw_hospital_vaccination_substitute');
        $this->addSql('DROP TABLE vaccination_contacts');
    }
}
