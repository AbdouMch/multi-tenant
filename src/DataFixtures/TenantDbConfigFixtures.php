<?php

namespace App\DataFixtures;

use App\Entity\Main\TenantDbConfig;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\Persistence\ObjectManager;
use Hakam\MultiTenancyBundle\Enum\DatabaseStatusEnum;
use Hakam\MultiTenancyBundle\Enum\DriverTypeEnum;

class TenantDbConfigFixtures extends Fixture implements FixtureGroupInterface
{
    private DsnParser $dsnParser;

    public function __construct(
        private readonly string $tenantDbUrl,
    )
    {
        $this->dsnParser = new DsnParser();
    }

    public function load(ObjectManager $manager): void
    {
        $dbNames = [
            'cabinet1',
            'cabinet2',
            'cabinet3',
            'cabinet4',
        ];

        $dbParams = $this->dsnParser->parse($this->tenantDbUrl);

        foreach ($dbNames as $dbName) {
            $newTenant = new TenantDbConfig();
            $newTenant->setDbName($dbName);
            $newTenant->setDbUserName($dbParams['user']);
            $newTenant->setDbHost($dbParams['host']);
            $newTenant->setDbPort($dbParams['port']);
            $newTenant->setDbPassword($dbParams['password']);
            $newTenant->setDriverType(DriverTypeEnum::from($dbParams['driver']));
            $newTenant->setDatabaseStatus(DatabaseStatusEnum::DATABASE_NOT_CREATED); // it will be switched to DATABASE_CREATED when we will execute the command
            $manager->persist($newTenant);
        }

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [
            'main'
        ];
    }
}
