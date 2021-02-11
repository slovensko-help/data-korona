<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210211104846 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audit_raw_iza_vaccinations (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_b7f6f72e8ec758abe70b5bf56bc4940e_idx (type), INDEX object_id_b7f6f72e8ec758abe70b5bf56bc4940e_idx (object_id), INDEX discriminator_b7f6f72e8ec758abe70b5bf56bc4940e_idx (discriminator), INDEX transaction_hash_b7f6f72e8ec758abe70b5bf56bc4940e_idx (transaction_hash), INDEX blame_id_b7f6f72e8ec758abe70b5bf56bc4940e_idx (blame_id), INDEX created_at_b7f6f72e8ec758abe70b5bf56bc4940e_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_raw_nczi_vaccinations (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_9b58bae2f6d55f7738bb3111a0516195_idx (type), INDEX object_id_9b58bae2f6d55f7738bb3111a0516195_idx (object_id), INDEX discriminator_9b58bae2f6d55f7738bb3111a0516195_idx (discriminator), INDEX transaction_hash_9b58bae2f6d55f7738bb3111a0516195_idx (transaction_hash), INDEX blame_id_9b58bae2f6d55f7738bb3111a0516195_idx (blame_id), INDEX created_at_9b58bae2f6d55f7738bb3111a0516195_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_raw_power_bi_vaccinations (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_d4e5a091ccad4e32092c5187df6af987_idx (type), INDEX object_id_d4e5a091ccad4e32092c5187df6af987_idx (object_id), INDEX discriminator_d4e5a091ccad4e32092c5187df6af987_idx (discriminator), INDEX transaction_hash_d4e5a091ccad4e32092c5187df6af987_idx (transaction_hash), INDEX blame_id_d4e5a091ccad4e32092c5187df6af987_idx (blame_id), INDEX created_at_d4e5a091ccad4e32092c5187df6af987_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_region_vaccinations (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_4b413bdf344f891789676e6b4c62bc26_idx (type), INDEX object_id_4b413bdf344f891789676e6b4c62bc26_idx (object_id), INDEX discriminator_4b413bdf344f891789676e6b4c62bc26_idx (discriminator), INDEX transaction_hash_4b413bdf344f891789676e6b4c62bc26_idx (transaction_hash), INDEX blame_id_4b413bdf344f891789676e6b4c62bc26_idx (blame_id), INDEX created_at_4b413bdf344f891789676e6b4c62bc26_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_slovakia_vaccinations (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_39daea63de5c152a883369c67183e75e_idx (type), INDEX object_id_39daea63de5c152a883369c67183e75e_idx (object_id), INDEX discriminator_39daea63de5c152a883369c67183e75e_idx (discriminator), INDEX transaction_hash_39daea63de5c152a883369c67183e75e_idx (transaction_hash), INDEX blame_id_39daea63de5c152a883369c67183e75e_idx (blame_id), INDEX created_at_39daea63de5c152a883369c67183e75e_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE audit_raw_iza_vaccinations');
        $this->addSql('DROP TABLE audit_raw_nczi_vaccinations');
        $this->addSql('DROP TABLE audit_raw_power_bi_vaccinations');
        $this->addSql('DROP TABLE audit_region_vaccinations');
        $this->addSql('DROP TABLE audit_slovakia_vaccinations');
    }
}
