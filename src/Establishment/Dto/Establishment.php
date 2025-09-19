<?php

namespace App\Establishment\Dto;

class Establishment
{
    public function __construct(
        public string $publicId,
        public string $name,
        public string $address,
    )
    {
    }
}