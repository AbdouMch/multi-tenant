<?php

namespace App\DataFixtures;

use App\Factory\PatientFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;
use Hakam\MultiTenancyBundle\Attribute\TenantFixture;

#[TenantFixture]
class PatientFixtures extends Fixture implements FixtureGroupInterface
{
    public function load(ObjectManager $manager): void
    {
        PatientFactory::createMany(5);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return [
            'tenant',
        ];
    }
}
