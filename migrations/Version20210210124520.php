<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210210124520 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE audit_hospital_beds (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_3fc4a3e3fb94ac28ba0412c9c15f1328_idx (type), INDEX object_id_3fc4a3e3fb94ac28ba0412c9c15f1328_idx (object_id), INDEX discriminator_3fc4a3e3fb94ac28ba0412c9c15f1328_idx (discriminator), INDEX transaction_hash_3fc4a3e3fb94ac28ba0412c9c15f1328_idx (transaction_hash), INDEX blame_id_3fc4a3e3fb94ac28ba0412c9c15f1328_idx (blame_id), INDEX created_at_3fc4a3e3fb94ac28ba0412c9c15f1328_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_hospital_patients (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_73e696b901aebfd8cf4b008305253ff2_idx (type), INDEX object_id_73e696b901aebfd8cf4b008305253ff2_idx (object_id), INDEX discriminator_73e696b901aebfd8cf4b008305253ff2_idx (discriminator), INDEX transaction_hash_73e696b901aebfd8cf4b008305253ff2_idx (transaction_hash), INDEX blame_id_73e696b901aebfd8cf4b008305253ff2_idx (blame_id), INDEX created_at_73e696b901aebfd8cf4b008305253ff2_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE audit_hospital_staff (id INT UNSIGNED AUTO_INCREMENT NOT NULL, type VARCHAR(10) NOT NULL, object_id VARCHAR(255) NOT NULL, discriminator VARCHAR(255) DEFAULT NULL, transaction_hash VARCHAR(40) DEFAULT NULL, diffs LONGTEXT DEFAULT NULL, blame_id VARCHAR(255) DEFAULT NULL, blame_user VARCHAR(255) DEFAULT NULL, blame_user_fqdn VARCHAR(255) DEFAULT NULL, blame_user_firewall VARCHAR(100) DEFAULT NULL, ip VARCHAR(45) DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX type_443bf600096337bd7661b89437d25ca5_idx (type), INDEX object_id_443bf600096337bd7661b89437d25ca5_idx (object_id), INDEX discriminator_443bf600096337bd7661b89437d25ca5_idx (discriminator), INDEX transaction_hash_443bf600096337bd7661b89437d25ca5_idx (transaction_hash), INDEX blame_id_443bf600096337bd7661b89437d25ca5_idx (blame_id), INDEX created_at_443bf600096337bd7661b89437d25ca5_idx (created_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE audit_hospital_beds');
        $this->addSql('DROP TABLE audit_hospital_patients');
        $this->addSql('DROP TABLE audit_hospital_staff');
    }
}
