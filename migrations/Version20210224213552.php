<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210224213552 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raw_hospital_vaccination_substitute ADD is_accepting_new_registrations TINYINT(1) NOT NULL, DROP region_name, DROP city_name, CHANGE hospital_name hospital_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE vaccination_contacts ADD is_accepting_new_registrations TINYINT(1) NOT NULL');
        $this->addSql('UPDATE raw_hospital_vaccination_substitute SET is_accepting_new_registrations = 1');
        $this->addSql('UPDATE vaccination_contacts SET is_accepting_new_registrations = 1');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raw_hospital_vaccination_substitute ADD region_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD city_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP is_accepting_new_registrations, CHANGE hospital_name hospital_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE vaccination_contacts DROP is_accepting_new_registrations');
    }
}
