<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210325180723 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE raw_power_bi_ag_tests (code VARCHAR(255) CHARACTER SET ascii NOT NULL, district_id INT NOT NULL, published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', positives_count INT NOT NULL, negatives_count INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_B6DF2640B08FA272 (district_id), INDEX IDX_B6DF264043625D9F (updated_at), PRIMARY KEY(code)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_raw_power_bi_ag_tests (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_65f2d2c6aef6812e2dcc55e6fd5d242f_idx (type), INDEX object_id_65f2d2c6aef6812e2dcc55e6fd5d242f_idx (object_id), INDEX discriminator_65f2d2c6aef6812e2dcc55e6fd5d242f_idx (discriminator), INDEX transaction_hash_65f2d2c6aef6812e2dcc55e6fd5d242f_idx (transaction_hash), INDEX blame_id_65f2d2c6aef6812e2dcc55e6fd5d242f_idx (blame_id), INDEX created_at_65f2d2c6aef6812e2dcc55e6fd5d242f_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE raw_power_bi_ag_tests ADD CONSTRAINT FK_B6DF2640B08FA272 FOREIGN KEY (district_id) REFERENCES district (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE raw_power_bi_ag_tests');
        $this->addSql('DROP TABLE audit_raw_power_bi_ag_tests');
    }
}
