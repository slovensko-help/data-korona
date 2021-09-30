<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210930145819 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slovakia_vaccinated_people ADD partially_vaccinated_patients_rate INT DEFAULT NULL, ADD unknown_dose_but_vaccinated_patients_rate INT DEFAULT NULL, CHANGE vaccinated_patients_rate fully_vaccinated_patients_rate INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE slovakia_vaccinated_people ADD vaccinated_patients_rate INT DEFAULT NULL, DROP fully_vaccinated_patients_rate, DROP partially_vaccinated_patients_rate, DROP unknown_dose_but_vaccinated_patients_rate');
    }
}
