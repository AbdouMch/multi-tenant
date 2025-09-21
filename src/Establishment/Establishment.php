<?php

namespace App\Establishment;

use ApiPlatform\Metadata\ApiResource;

#[ApiResource(operations: [])]
class Establishment
{
    public function __construct(
        public string $id,
        public string $name,
        public string $address,
    )
    {
    }
}