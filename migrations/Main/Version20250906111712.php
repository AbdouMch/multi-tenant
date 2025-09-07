<?php

declare(strict_types=1);

namespace DoctrineMigrations\Main;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250906111712 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Main tables';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE `User` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, establishment_id INT DEFAULT NULL, INDEX IDX_2DA179778565851 (establishment_id), UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE establishment (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, tenant_id BIGINT NOT NULL, address VARCHAR(255) NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE tenant_db_config (id INT AUTO_INCREMENT NOT NULL, db_name VARCHAR(255) NOT NULL, driver_type VARCHAR(255) DEFAULT \'mysql\' NOT NULL, db_user_name VARCHAR(255) DEFAULT NULL, db_password VARCHAR(255) DEFAULT NULL, db_host VARCHAR(255) DEFAULT NULL, db_port VARCHAR(5) DEFAULT NULL, database_status VARCHAR(255) DEFAULT \'DATABASE_NOT_CREATED\' NOT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE `User` ADD CONSTRAINT FK_2DA179778565851 FOREIGN KEY (establishment_id) REFERENCES establishment (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE `User` DROP FOREIGN KEY FK_2DA179778565851');
        $this->addSql('DROP TABLE `User`');
        $this->addSql('DROP TABLE establishment');
        $this->addSql('DROP TABLE tenant_db_config');
    }
}
