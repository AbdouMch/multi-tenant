<?php

namespace App\DataFixtures;

use App\Entity\Main\Establishment;
use App\Factory\EstablishmentFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Persistence\ObjectManager;

class EstablishmentFixtures extends Fixture implements FixtureGroupInterface
{
    public const ESTABLISHMENT_REFERENCE = 'establishment';

    public function load(ObjectManager $manager): void
    {
        $establishments = EstablishmentFactory::createSequence(
            function() {
                foreach (range(1, 4) as $i) {
                    yield ['tenantId' => $i];
                }
            }
        );

        /** @var Establishment $establishment */
        foreach ($establishments as $establishment) {
            $this->addReference(self::ESTABLISHMENT_REFERENCE . $establishment->getTenantId(), $establishment);
        }

        $manager->flush();
    }


    public static function getGroups(): array
    {
        return ['main'];
    }
}
