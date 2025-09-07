<?php

namespace App\DataFixtures;

use App\Entity\Main\Establishment;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture implements FixtureGroupInterface, DependentFixtureInterface
{
    private const USER_REFERENCE = 'user_';

    public function load(ObjectManager $manager): void
    {
        UserFactory::createSequence(
            [
                ['establishment' => $this->getReference(EstablishmentFixtures::ESTABLISHMENT_REFERENCE . 1, Establishment::class)],
                ['establishment' => $this->getReference(EstablishmentFixtures::ESTABLISHMENT_REFERENCE . 2, Establishment::class)],
                ['establishment' => $this->getReference(EstablishmentFixtures::ESTABLISHMENT_REFERENCE . 3, Establishment::class)],
                ['establishment' => $this->getReference(EstablishmentFixtures::ESTABLISHMENT_REFERENCE . 4, Establishment::class)],
                ['establishment' => $this->getReference(EstablishmentFixtures::ESTABLISHMENT_REFERENCE . 4, Establishment::class)],
            ]
        );

        $admin = UserFactory::createOne([
            'roles' => ['ROLE_ADMIN'],
        ]);

        $this->addReference(self::USER_REFERENCE . 'admin', $admin);

        $manager->flush();
    }

    public static function getGroups(): array
    {
        return ['main'];
    }

    public function getDependencies(): array
    {
        return [
            EstablishmentFixtures::class,
        ];
    }
}
