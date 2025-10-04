<?php

namespace App\ApiResource\Main;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Main\Establishment as EstablishmentEntity;
use App\Provider\Main\EstablishmentProvider;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/establishments/{id}',
            uriVariables: [
                'id' => 'id',
            ],
            requirements: ['id' => '[a-zA-Z0-9_-]+'],
        ),
        new GetCollection(
            uriTemplate: '/establishments',
            itemUriTemplate: '/establishments/{id}',
        ),
    ],
    security: "is_granted('ROLE_ADMIN')",
    provider: EstablishmentProvider::class,
    stateOptions: new Options(EstablishmentEntity::class),
)]
class Establishment
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string $id,
        public string $name,
        public string $address,
    )
    {
    }
}