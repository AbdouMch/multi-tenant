<?php

namespace App\DBAL\Type;

use App\DBAL\Mapping\GeneratedPublicId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class GeneratedPublicIdType extends StringType
{
    public const NAME = 'generated_public_id';

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if ($value === null) {
            throw new \InvalidArgumentException(sprintf(
                "%s value cannot be null. Maybe you forgot to add the %s attribute.",
                self::class,
                GeneratedPublicId::class
            ));
        }

        return parent::convertToDatabaseValue($value, $platform);
    }
}