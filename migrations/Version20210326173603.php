<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210326173603 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE district_ag_tests (id VARCHAR(255) CHARACTER SET ascii NOT NULL, district_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', positives_count INT DEFAULT NULL, negatives_count INT DEFAULT NULL, positives_sum INT DEFAULT NULL, negatives_sum INT DEFAULT NULL, positivity_rate NUMERIC(6, 3) DEFAULT NULL, INDEX IDX_463C29ABB08FA272 (district_id), INDEX IDX_463C29AB43625D9F (updated_at), INDEX IDX_463C29AB83352915 (published_on), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_district_ag_tests (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_b1140b2fc9cecd0019a76b0fc89dc19d_idx (type), INDEX object_id_b1140b2fc9cecd0019a76b0fc89dc19d_idx (object_id), INDEX discriminator_b1140b2fc9cecd0019a76b0fc89dc19d_idx (discriminator), INDEX transaction_hash_b1140b2fc9cecd0019a76b0fc89dc19d_idx (transaction_hash), INDEX blame_id_b1140b2fc9cecd0019a76b0fc89dc19d_idx (blame_id), INDEX created_at_b1140b2fc9cecd0019a76b0fc89dc19d_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE region_ag_tests (id VARCHAR(255) CHARACTER SET ascii NOT NULL, region_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', positives_count INT DEFAULT NULL, negatives_count INT DEFAULT NULL, positives_sum INT DEFAULT NULL, negatives_sum INT DEFAULT NULL, positivity_rate NUMERIC(6, 3) DEFAULT NULL, INDEX IDX_6D2FB8B498260155 (region_id), INDEX IDX_6D2FB8B443625D9F (updated_at), INDEX IDX_6D2FB8B483352915 (published_on), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_region_ag_tests (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_f7e212b86e46015c8979bf01fe8bde7b_idx (type), INDEX object_id_f7e212b86e46015c8979bf01fe8bde7b_idx (object_id), INDEX discriminator_f7e212b86e46015c8979bf01fe8bde7b_idx (discriminator), INDEX transaction_hash_f7e212b86e46015c8979bf01fe8bde7b_idx (transaction_hash), INDEX blame_id_f7e212b86e46015c8979bf01fe8bde7b_idx (blame_id), INDEX created_at_f7e212b86e46015c8979bf01fe8bde7b_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE district_ag_tests ADD CONSTRAINT FK_463C29ABB08FA272 FOREIGN KEY (district_id) REFERENCES district (id)');
        $this->addSql('ALTER TABLE region_ag_tests ADD CONSTRAINT FK_6D2FB8B498260155 FOREIGN KEY (region_id) REFERENCES region (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE district_ag_tests');
        $this->addSql('DROP TABLE audit_district_ag_tests');
        $this->addSql('DROP TABLE region_ag_tests');
        $this->addSql('DROP TABLE audit_region_ag_tests');
    }
}
