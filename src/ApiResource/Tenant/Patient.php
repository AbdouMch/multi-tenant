<?php

namespace App\ApiResource\Tenant;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Tenant\Provider\PatientRepresentation;

#[ApiResource(
    operations: [
        new GetCollection(
            uriTemplate: '/admin/tenants/{tenantId}/patients',
            uriVariables: [
                'tenantId',
            ],
            security: "is_granted('ROLE_SUPER_ADMIN')",
            provider: PatientRepresentation::class,
        ),
        new Get(
            uriTemplate: '/admin/tenants/{tenantId}/patients/{id}',
            uriVariables: [
                'tenantId',
                'id',
            ],
            security: "is_granted('ROLE_SUPER_ADMIN')",
            provider: PatientRepresentation::class,
        ),
    ],
)]
#[ApiResource(
    operations: [
        new Get(
            uriTemplate: '/patients/{id}',
            requirements: ['id' => '[a-zA-Z0-9_-]+'],
        ),
        new GetCollection(
            uriTemplate: '/patients',
        )
    ],
    security: "is_granted('ROLE_TENANT_ADMIN')",
    provider: PatientRepresentation::class,
)]
class Patient
{
    public function __construct(
        #[ApiProperty(identifier: true)]
        public string             $id,
        public string             $firstname,
        public string             $lastname,
        public \DateTimeImmutable $birthdate,
        #[ApiProperty(security: "is_granted('ROLE_PATIENT_READ_ALL')")]
        public ?string            $nir = null,
        #[ApiProperty(readable: false)]
        public ?string            $tenantId = null,
    )
    {
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }
}