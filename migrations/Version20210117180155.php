<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210117180155 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hospital_staff (id INT UNSIGNED NOT NULL, hospital_id INT DEFAULT NULL, reported_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', out_of_work_ratio_doctor DOUBLE PRECISION DEFAULT NULL, out_of_work_ratio_nurse DOUBLE PRECISION DEFAULT NULL, out_of_work_ratio_other DOUBLE PRECISION DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_4334633E63DBB69 (hospital_id), INDEX IDX_4334633E43625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hospital_staff ADD CONSTRAINT FK_4334633E63DBB69 FOREIGN KEY (hospital_id) REFERENCES hospital (id)');
        $this->addSql('DROP TABLE cache_items');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cache_items (item_id VARBINARY(255) NOT NULL, item_data MEDIUMBLOB NOT NULL, item_lifetime INT UNSIGNED DEFAULT NULL, item_time INT UNSIGNED NOT NULL, PRIMARY KEY(item_id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('DROP TABLE hospital_staff');
    }
}
