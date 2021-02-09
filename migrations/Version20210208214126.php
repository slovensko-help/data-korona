<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210208214126 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE vaccinations (id VARCHAR(255) CHARACTER SET ascii NOT NULL, vaccine_id INT NOT NULL, region_id INT DEFAULT NULL, dose1_count INT DEFAULT NULL, dose2_count INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_92C6ED722BFE75C3 (vaccine_id), INDEX IDX_92C6ED7298260155 (region_id), INDEX IDX_92C6ED7243625D9F (updated_at), INDEX IDX_92C6ED7283352915 (published_on), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE vaccinations ADD CONSTRAINT FK_92C6ED722BFE75C3 FOREIGN KEY (vaccine_id) REFERENCES vaccine (id)');
        $this->addSql('ALTER TABLE vaccinations ADD CONSTRAINT FK_92C6ED7298260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE raw_nczi_vaccinations CHANGE dose1_sum dose1_sum INT DEFAULT NULL, CHANGE dose2_sum dose2_sum INT DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE vaccinations');
        $this->addSql('ALTER TABLE raw_nczi_vaccinations CHANGE dose1_sum dose1_sum INT NOT NULL, CHANGE dose2_sum dose2_sum INT NOT NULL');
    }
}
