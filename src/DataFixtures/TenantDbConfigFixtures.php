<?php

namespace App\DataFixtures;

use App\Factory\TenantDbConfigFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class TenantDbConfigFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        $dbNames = [
            'cabinet1',
            'cabinet2',
            'cabinet3',
            'cabinet4',
        ];


        foreach ($dbNames as $dbName) {
            TenantDbConfigFactory::createOne([
                'dbName' => $dbName,
            ]);
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
