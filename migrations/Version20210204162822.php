<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20210204162822 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        $this->addSql('RENAME TABLE `raw_slovakia_nczi_vaccinations` TO `raw_nczi_vaccinations`');
        $this->addSql('CREATE TABLE vaccine (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) CHARACTER SET ascii NOT NULL, title VARCHAR(255) NOT NULL, manufacturer VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_A7DD90B177153098 (code), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('RENAME TABLE `raw_nczi_vaccinations` TO `raw_slovakia_nczi_vaccinations`');
        $this->addSql('DROP TABLE vaccine');
    }
}
