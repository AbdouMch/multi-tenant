<?php

namespace App\ApiResource\Tenant;

use ApiPlatform\Doctrine\Orm\State\Options;
use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\Tenant\Patient as PatientEntity;
use App\Provider\Tenant\PatientProvider;
use Symfony\Component\Serializer\Attribute\SerializedName;

#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/admin/tenants/{tenantId}/patients/{id}',
            uriVariables: [
                'tenantId' => 'tenantId',
                'id' => 'publicId',
            ],
            requirements: ['id' => '[a-zA-Z0-9_-]+', 'tenantId' => '[a-zA-Z0-9_-]+'],
        ),
        new GetCollection(
            uriTemplate: '/admin/tenants/{tenantId}/patients',
            uriVariables: [
                'tenantId' => 'tenantId',
            ],
            requirements: ['tenantId' => '[a-zA-Z0-9_-]+'],
            itemUriTemplate: '/admin/tenants/{tenantId}/patients/{id}',
        ),
    ],
    security: "is_granted('ROLE_SUPER_ADMIN')",
    provider: PatientProvider::class,
    stateOptions: new Options(PatientEntity::class),
)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/patients/{id}',
            uriVariables: [
                'id' => 'publicId',
            ],
            requirements: ['id' => '[a-zA-Z0-9_-]+'],
        ),
        new GetCollection(
            uriTemplate: '/patients',
        )
    ],
    security: "is_granted('ROLE_TENANT_ADMIN')",
    provider: PatientProvider::class,
    stateOptions: new Options(PatientEntity::class),
)]
class Patient
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        #[SerializedName('id')]
        public string             $publicId,
        public string             $firstname,
        public string             $lastname,
        public \DateTimeImmutable $birthdate,
        #[ApiProperty(security: "is_granted('ROLE_PATIENT_READ_ALL')")]
        public ?string            $nir = null,
        #[ApiProperty(readable: false, identifier: false)]
        public ?string            $tenantId = null,
    )
    {
    }
}