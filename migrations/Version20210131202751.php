<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210131202751 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE INDEX IDX_B70BCD3B83352915 ON hospital_beds (published_on)');
        $this->addSql('CREATE INDEX IDX_651B8D983352915 ON hospital_patients (published_on)');
        $this->addSql('CREATE INDEX IDX_4334633E83352915 ON hospital_staff (published_on)');
        $this->addSql('CREATE INDEX IDX_CFC176AA83352915 ON raw_nczi_morning_email (published_on)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_B70BCD3B83352915 ON hospital_beds');
        $this->addSql('DROP INDEX IDX_651B8D983352915 ON hospital_patients');
        $this->addSql('DROP INDEX IDX_4334633E83352915 ON hospital_staff');
        $this->addSql('DROP INDEX IDX_CFC176AA83352915 ON raw_nczi_morning_email');
    }
}
