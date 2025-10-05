<?php

declare(strict_types=1);

namespace DoctrineMigrations\Tenant;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251004141821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Fix unique columns';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_84C95DF83A00E68 ON tenant_patient');
        $this->addSql('ALTER TABLE tenant_patient CHANGE publicId publicId VARCHAR(12) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_84C95DF9071995 ON tenant_patient (publicId)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP INDEX UNIQ_84C95DF9071995 ON tenant_patient');
        $this->addSql('ALTER TABLE tenant_patient CHANGE publicId publicId VARCHAR(22) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_84C95DF83A00E68 ON tenant_patient (firstname)');
    }
}
