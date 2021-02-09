<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210209180032 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE region_vaccinations (id VARCHAR(255) CHARACTER SET ascii NOT NULL, region_id INT DEFAULT NULL, dose1_count INT DEFAULT NULL, dose2_count INT DEFAULT NULL, dose1_sum INT DEFAULT NULL, dose2_sum INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_F417FA8D98260155 (region_id), INDEX IDX_F417FA8D43625D9F (updated_at), INDEX IDX_F417FA8D83352915 (published_on), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE slovakia_vaccinations (id VARCHAR(255) CHARACTER SET ascii NOT NULL, dose1_count INT DEFAULT NULL, dose2_count INT DEFAULT NULL, dose1_sum INT DEFAULT NULL, dose2_sum INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_744C1B9943625D9F (updated_at), INDEX IDX_744C1B9983352915 (published_on), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE region_vaccinations ADD CONSTRAINT FK_F417FA8D98260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('DROP TABLE raw_power_bi_vaccinations_by_region');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE raw_power_bi_vaccinations_by_region (code VARCHAR(255) CHARACTER SET ascii NOT NULL COLLATE `ascii_general_ci`, region_id INT NOT NULL, published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', dose1_count INT NOT NULL, dose2_count INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_1866FC698260155 (region_id), INDEX IDX_1866FC643625D9F (updated_at), PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations_by_region ADD CONSTRAINT FK_1866FC698260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('DROP TABLE region_vaccinations');
        $this->addSql('DROP TABLE slovakia_vaccinations');
    }
}
