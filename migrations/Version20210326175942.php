<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210326175942 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE district_ag_tests CHANGE positivity_rate positivity_rate INT DEFAULT NULL');
        $this->addSql('ALTER TABLE region_ag_tests CHANGE positivity_rate positivity_rate INT DEFAULT NULL');
        $this->addSql('ALTER TABLE slovakia_ag_tests CHANGE positivity_rate positivity_rate INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE district_ag_tests CHANGE positivity_rate positivity_rate NUMERIC(6, 3) DEFAULT NULL');
        $this->addSql('ALTER TABLE region_ag_tests CHANGE positivity_rate positivity_rate NUMERIC(6, 3) DEFAULT NULL');
        $this->addSql('ALTER TABLE slovakia_ag_tests CHANGE positivity_rate positivity_rate NUMERIC(6, 3) DEFAULT NULL');
    }
}
