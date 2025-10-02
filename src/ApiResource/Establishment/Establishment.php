<?php

namespace App\ApiResource\Establishment;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Main\Provider\EstablishmentRepresentation;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/establishments/{id}',
            requirements: ['id' => '[a-zA-Z0-9_-]+'],
        ),
        new GetCollection(
            uriTemplate: '/establishments',
            itemUriTemplate: '/establishments/{id}',
        ),
    ],
    security: "is_granted('ROLE_ADMIN')",
    provider: EstablishmentRepresentation::class,
)]
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