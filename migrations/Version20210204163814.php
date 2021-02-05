<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210204163814 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_4285551c43625d9f ON raw_nczi_vaccinations');
        $this->addSql('CREATE INDEX IDX_31B65F6243625D9F ON raw_nczi_vaccinations (updated_at)');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations DROP FOREIGN KEY FK_7B0C5FD998260155');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations ADD vaccine_id INT NOT NULL, DROP vaccine_title, DROP vaccine_manufacturer');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations ADD CONSTRAINT FK_F2304662BFE75C3 FOREIGN KEY (vaccine_id) REFERENCES vaccine (id)');
        $this->addSql('CREATE INDEX IDX_F2304662BFE75C3 ON raw_power_bi_vaccinations (vaccine_id)');
        $this->addSql('DROP INDEX idx_7b0c5fd998260155 ON raw_power_bi_vaccinations');
        $this->addSql('CREATE INDEX IDX_F23046698260155 ON raw_power_bi_vaccinations (region_id)');
        $this->addSql('DROP INDEX idx_7b0c5fd943625d9f ON raw_power_bi_vaccinations');
        $this->addSql('CREATE INDEX IDX_F23046643625D9F ON raw_power_bi_vaccinations (updated_at)');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations ADD CONSTRAINT FK_7B0C5FD998260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE vaccine CHANGE code code VARCHAR(255) CHARACTER SET ASCII NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX idx_31b65f6243625d9f ON raw_nczi_vaccinations');
        $this->addSql('CREATE INDEX IDX_4285551C43625D9F ON raw_nczi_vaccinations (updated_at)');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations DROP FOREIGN KEY FK_F2304662BFE75C3');
        $this->addSql('DROP INDEX IDX_F2304662BFE75C3 ON raw_power_bi_vaccinations');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations DROP FOREIGN KEY FK_F23046698260155');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations ADD vaccine_title VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, ADD vaccine_manufacturer VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP vaccine_id');
        $this->addSql('DROP INDEX idx_f23046698260155 ON raw_power_bi_vaccinations');
        $this->addSql('CREATE INDEX IDX_7B0C5FD998260155 ON raw_power_bi_vaccinations (region_id)');
        $this->addSql('DROP INDEX idx_f23046643625d9f ON raw_power_bi_vaccinations');
        $this->addSql('CREATE INDEX IDX_7B0C5FD943625D9F ON raw_power_bi_vaccinations (updated_at)');
        $this->addSql('ALTER TABLE raw_power_bi_vaccinations ADD CONSTRAINT FK_F23046698260155 FOREIGN KEY (region_id) REFERENCES region (id)');
        $this->addSql('ALTER TABLE vaccine CHANGE code code VARCHAR(255) CHARACTER SET ascii NOT NULL COLLATE `ascii_general_ci`');
    }
}
