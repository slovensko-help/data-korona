<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210325192715 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE raw_nczi_ag_tests (id INT NOT NULL, published_on DATE NOT NULL COMMENT \'(DC2Type:date_immutable)\', positives_count INT NOT NULL, negatives_count INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_4E0BB99143625D9F (updated_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_raw_nczi_ag_tests (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_2c83fdbc4a0662cec2fe274121512ba2_idx (type), INDEX object_id_2c83fdbc4a0662cec2fe274121512ba2_idx (object_id), INDEX discriminator_2c83fdbc4a0662cec2fe274121512ba2_idx (discriminator), INDEX transaction_hash_2c83fdbc4a0662cec2fe274121512ba2_idx (transaction_hash), INDEX blame_id_2c83fdbc4a0662cec2fe274121512ba2_idx (blame_id), INDEX created_at_2c83fdbc4a0662cec2fe274121512ba2_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE raw_nczi_ag_tests');
        $this->addSql('DROP TABLE audit_raw_nczi_ag_tests');
    }
}
