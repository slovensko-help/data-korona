<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210131174215 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('RENAME TABLE `nczi_morning_email` TO `raw_nczi_morning_email`');
        $this->addSql('RENAME TABLE `slovakia_nczi_vaccinations` TO `raw_slovakia_nczi_vaccinations`');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('RENAME TABLE `raw_nczi_morning_email` TO `nczi_morning_email`');
        $this->addSql('RENAME TABLE `raw_slovakia_nczi_vaccinations` TO `slovakia_nczi_vaccinations`');
    }
}
