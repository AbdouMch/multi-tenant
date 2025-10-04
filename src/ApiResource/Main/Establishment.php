<?php

namespace App\ApiResource\Main;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Main\Establishment as EstablishmentEntity;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/establishments/{id}',
            uriVariables: [
                'id' => 'publicId',
            ],
            requirements: ['id' => '[a-zA-Z0-9_-]+'],
        ),
        new GetCollection(
            uriTemplate: '/establishments',
            itemUriTemplate: '/establishments/{id}',
        ),
    ],
    security: "is_granted('ROLE_ADMIN')",
    stateOptions: new Options(EstablishmentEntity::class),
)]
class Establishment
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        #[SerializedName('id')]
        public string             $publicId,
        public string $name,
        public string $address,
    )
    {
    }
}