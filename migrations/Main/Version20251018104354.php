<?php

declare(strict_types=1);

namespace DoctrineMigrations\Main;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251018104354 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE log_entry (data JSON DEFAULT NULL, id INT AUTO_INCREMENT NOT NULL, action VARCHAR(8) NOT NULL, logged_at DATETIME NOT NULL, object_id VARCHAR(64) DEFAULT NULL, object_class VARCHAR(191) NOT NULL, version INT NOT NULL, username VARCHAR(191) DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE User ADD created_at DATETIME, ADD updated_at DATETIME');
        $this->addSql('UPDATE User SET created_at=NOW() , updated_at=NOW()');
        $this->addSql('ALTER TABLE User CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');

        $this->addSql('ALTER TABLE establishment ADD created_at DATETIME, ADD updated_at DATETIME');
        $this->addSql('UPDATE establishment SET created_at=NOW() , updated_at=NOW()');
        $this->addSql('ALTER TABLE establishment CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');

        $this->addSql('ALTER TABLE tenant_db_config ADD created_at DATETIME, ADD updated_at DATETIME');
        $this->addSql('UPDATE tenant_db_config SET created_at=NOW() , updated_at=NOW()');
        $this->addSql('ALTER TABLE tenant_db_config CHANGE created_at created_at DATETIME NOT NULL, CHANGE updated_at updated_at DATETIME NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE log_entry');
        $this->addSql('ALTER TABLE `User` DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE establishment DROP created_at, DROP updated_at');
        $this->addSql('ALTER TABLE tenant_db_config DROP created_at, DROP updated_at');
    }
}
