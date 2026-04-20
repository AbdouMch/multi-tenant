<?php

namespace App\Factory;

use App\Entity\Tenant\Patient;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Patient>
 */
final class PatientFactory extends PersistentObjectFactory
{
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Patient::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'birthDate' => \DateTimeImmutable::createFromMutable(self::faker()->dateTime()),
            'firstname' => self::faker()->firstName(),
            'lastname'  => self::faker()->lastName(),
            // French NIR: 13 digits. Left-pad to ensure correct length.
            'nir'       => str_pad((string) self::faker()->randomNumber(8), 13, '0', STR_PAD_LEFT),
        ];
    }

    protected function initialize(): static
    {
        return $this;
    }
}
