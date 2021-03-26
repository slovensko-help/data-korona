<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210325211654 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ag_tests (id VARCHAR(255) CHARACTER SET ascii NOT NULL, district_id INT DEFAULT NULL, positives_count INT DEFAULT NULL, negatives_count INT DEFAULT NULL, positivity_rate NUMERIC(5, 3) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', INDEX IDX_1EE8BC46B08FA272 (district_id), INDEX IDX_1EE8BC4643625D9F (updated_at), INDEX IDX_1EE8BC4683352915 (published_on), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_ag_tests (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_44e2455b894c6d03dd4a1f91a7722bf1_idx (type), INDEX object_id_44e2455b894c6d03dd4a1f91a7722bf1_idx (object_id), INDEX discriminator_44e2455b894c6d03dd4a1f91a7722bf1_idx (discriminator), INDEX transaction_hash_44e2455b894c6d03dd4a1f91a7722bf1_idx (transaction_hash), INDEX blame_id_44e2455b894c6d03dd4a1f91a7722bf1_idx (blame_id), INDEX created_at_44e2455b894c6d03dd4a1f91a7722bf1_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ag_tests ADD CONSTRAINT FK_1EE8BC46B08FA272 FOREIGN KEY (district_id) REFERENCES district (id)');
        $this->addSql('ALTER TABLE raw_nczi_ag_tests ADD positives_sum INT NOT NULL, ADD negatives_sum INT NOT NULL, DROP positives_count, DROP negatives_count');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ag_tests');
        $this->addSql('DROP TABLE audit_ag_tests');
        $this->addSql('ALTER TABLE raw_nczi_ag_tests ADD positives_count INT NOT NULL, ADD negatives_count INT NOT NULL, DROP positives_sum, DROP negatives_sum');
    }
}
