<?php

declare(strict_types=1);

namespace DoctrineMigrations\Main;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260420155725 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add enabled status to User entity';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE User ADD enabled TINYINT(1) DEFAULT 0 NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `User` DROP enabled');
    }
}
