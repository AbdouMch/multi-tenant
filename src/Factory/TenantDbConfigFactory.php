<?php

namespace App\Factory;

use App\Entity\Main\TenantDbConfig;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Tools\DsnParser;
use Hakam\MultiTenancyBundle\Enum\DatabaseStatusEnum;
use Hakam\MultiTenancyBundle\Enum\DriverTypeEnum;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<TenantDbConfig>
 */
final class TenantDbConfigFactory extends PersistentObjectFactory
{
    /**
     * @var string[]
     */
    private array $dbParams;

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     */
    public function __construct(
        private readonly string $tenantDbUrl)
    {
        parent::__construct();
    }

    public static function class(): string
    {
        return TenantDbConfig::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     */
    protected function defaults(): array|callable
    {
        $dsnParser = new DsnParser();
        $this->dbParams = $dsnParser->parse($this->tenantDbUrl);

        return [
            'dbUserName' => $this->dbParams['user'],
            'dbHost' => $this->dbParams['host'],
            'dbPort' => $this->dbParams['port'],
            'dbPassword' => $this->dbParams['password'],
            'driverType' => DriverTypeEnum::from($this->dbParams['driver']),
            'databaseStatus' => DatabaseStatusEnum::DATABASE_NOT_CREATED, // it will be switched to DATABASE_CREATED when we will execute the command
        ];
    }

    public function create(callable|array $attributes = []): object
    {
        if (!isset($attributes['dbName'])) {
            throw new \InvalidArgumentException("Missing 'dbName' attribute");
        }

        $attributes = array_merge($this->defaults(), $attributes);

        $dbName = $attributes['dbName'];
        $dbUser = $attributes['dbUserName'];
        $dbPass = $attributes['dbPassword'];

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_mysql',
            'host' => $this->dbParams['host'],
            'port' => $this->dbParams['port'],
            'user' => $this->dbParams['user'],
            'password' => $this->dbParams['password'],
            'charset' => 'utf8',
        ]);

        $connection->executeQuery(sprintf("CREATE DATABASE IF NOT EXISTS %s", $dbName));
        $connection->executeQuery(sprintf("CREATE USER IF NOT EXISTS '%s'@'%%' IDENTIFIED BY '%s'", $dbUser, $dbPass));
        $connection->executeQuery(sprintf("GRANT SELECT, INSERT, UPDATE, DELETE, CREATE, ALTER, INDEX ON %s.* TO '%s'@'%%'", $dbName, $dbUser));
        $connection->executeQuery("FLUSH PRIVILEGES");

        return parent::create($attributes);
    }
}
