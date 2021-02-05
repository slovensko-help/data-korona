<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210204173250 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations ADD code VARCHAR(255) NOT NULL, DROP id');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations ADD PRIMARY KEY (code)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations DROP PRIMARY KEY');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations ADD id INT NOT NULL, DROP code');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations ADD PRIMARY KEY (id)');
    }
}
