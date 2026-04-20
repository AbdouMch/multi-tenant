<?php

namespace App\Service;

use App\Entity\Main\TenantDbConfig;
use App\Message\CreateTenantDbMessage;
use Doctrine\DBAL\Tools\DsnParser;
use Hakam\MultiTenancyBundle\Enum\DatabaseStatusEnum;
use Hakam\MultiTenancyBundle\Enum\DriverTypeEnum;
use Symfony\Component\Messenger\MessageBusInterface;

class TenantProvisioningService
{
    private ?array $dbParams = null;

    public function __construct(
        private readonly MessageBusInterface $messageBus,
        private readonly string              $tenantDbUrl,
    ) {
    }

    private function getDbParams(): array
    {
        return $this->dbParams ??= (new DsnParser(['mysql' => 'pdo_mysql', 'postgresql' => 'pdo_pgsql']))
            ->parse($this->tenantDbUrl);
    }

    /**
     * Builds and persists a TenantDbConfig for a new tenant database.
     * Does NOT flush — callers manage the transaction boundary.
     */
    public function buildDbConfig(string $dbName): TenantDbConfig
    {
        $dbConfig = new TenantDbConfig();
        $dbConfig->setDbName($dbName);
        $dbConfig->setDbUserName($dbName . '_user');
        $dbConfig->setDbPassword(bin2hex(random_bytes(32)));
        $params = $this->getDbParams();
        $dbConfig->setDbHost((string) ($params['host'] ?? '127.0.0.1'));
        $dbConfig->setDbPort((string) ($params['port'] ?? 3306));
        $dbConfig->setDriverType(DriverTypeEnum::from($params['driver']));
        $dbConfig->setDatabaseStatus(DatabaseStatusEnum::DATABASE_NOT_CREATED);

        return $dbConfig;
    }

    /**
     * Dispatches the async message that provisions the actual database.
     * Must be called AFTER the transaction has committed so the handler
     * can read the persisted TenantDbConfig.
     */
    public function dispatchProvisioning(TenantDbConfig $dbConfig): void
    {
        $this->messageBus->dispatch(new CreateTenantDbMessage((string) $dbConfig->getId()));
    }
}