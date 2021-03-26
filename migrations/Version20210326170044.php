<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210326170044 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE slovakia_ag_tests (id VARCHAR(255) CHARACTER SET ascii NOT NULL, positives_count INT DEFAULT NULL, negatives_count INT DEFAULT NULL, positives_sum INT DEFAULT NULL, negatives_sum INT DEFAULT NULL, positivity_rate NUMERIC(6, 3) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_2D81A52E43625D9F (updated_at), INDEX IDX_2D81A52E83352915 (published_on), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_slovakia_ag_tests (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_85197a067c004af43d0de9e5257f95f3_idx (type), INDEX object_id_85197a067c004af43d0de9e5257f95f3_idx (object_id), INDEX discriminator_85197a067c004af43d0de9e5257f95f3_idx (discriminator), INDEX transaction_hash_85197a067c004af43d0de9e5257f95f3_idx (transaction_hash), INDEX blame_id_85197a067c004af43d0de9e5257f95f3_idx (blame_id), INDEX created_at_85197a067c004af43d0de9e5257f95f3_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE slovakia_ag_tests');
        $this->addSql('DROP TABLE audit_slovakia_ag_tests');
    }
}
