<?php

namespace App\Tenant;

use App\Entity\Main\TenantDbConfig;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Hakam\MultiTenancyBundle\Services\DbService;

class TenantDatabaseManager
{
    public function __construct(
        private readonly DbService $dbService,
        private readonly string    $tenantDbUrl,
    ) {
    }

    public function createDatabase(TenantDbConfig $dbConfig): void
    {
        $this->dbService->createDatabase($dbConfig);

        $dsnParser = new DsnParser([
            'mysql'      => 'pdo_mysql',
            'postgresql' => 'pdo_pgsql',
        ]);

        $connection = DriverManager::getConnection($dsnParser->parse($this->tenantDbUrl));

        // DDL statements do not support prepared-statement placeholders for identifiers,
        // so we enforce a strict allowlist before interpolating into SQL.
        $dbName   = $this->sanitizeIdentifier($dbConfig->getDbName());
        $userName = $this->sanitizeIdentifier($dbConfig->getDbUserName());
        $password = $dbConfig->getDbPassword();

        $connection->executeQuery(sprintf(
            "CREATE USER IF NOT EXISTS '%s'@'%%' IDENTIFIED BY '%s'",
            $userName,
            addslashes($password),
        ));
        $connection->executeQuery(sprintf(
            'GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX ON `%s`.* TO \'%s\'@\'%%\'',
            $dbName,
            $userName,
        ));
        $connection->executeQuery('FLUSH PRIVILEGES');
        $connection->close();
    }

    /**
     * Restricts a database identifier to alphanumerics and underscores
     * to prevent SQL injection in DDL statements.
     */
    private function sanitizeIdentifier(string $identifier): string
    {
        if (!preg_match('/^[a-zA-Z0-9_]{1,64}$/', $identifier)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid database identifier "%s": only alphanumerics and underscores are allowed.', $identifier)
            );
        }

        return $identifier;
    }
}