<?php

namespace App\DBAL\Mapping;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class GeneratedPublicId
{
    /**
     * @param int|null $length Optional custom length. If null, defaults to default values depending on moreEntropy.
     */
    public function __construct(
        public readonly bool $moreEntropy = false,
        public readonly ?int $length = null,
    )
    {
    }
}