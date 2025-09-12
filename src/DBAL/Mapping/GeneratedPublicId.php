<?php

namespace App\DBAL\Mapping;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class GeneratedPublicId
{
    public const  DEFAULT_LENGTH = 12;

    public function __construct(
        public readonly int $length = self::DEFAULT_LENGTH,
    ) {
    }
}