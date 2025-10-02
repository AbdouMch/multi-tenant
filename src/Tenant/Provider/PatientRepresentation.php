<?php

namespace App\Tenant\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Tenant\Patient;
use App\Security\TenantContext;
use App\Tenant\Repository\PatientRepository;

class PatientRepresentation implements ProviderInterface
{
    public function __construct(
        private readonly TenantContext $tenantContext,
        private readonly PatientRepository $patientRepo,
    )
    {
    }

    /**
     * @return Patient|Patient[]|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $tenantId = $uriVariables['tenantId'] ?? null;

        if (null !== $tenantId) {
            dump($this->tenantContext->hasTenant());
            $this->tenantContext->setTenantIdByPublicId($tenantId);
        }

        if (false === $this->tenantContext->hasTenant()) {
            throw new \LogicException('Unable to detect the tenant id.');
        }

        if ($operation instanceof CollectionOperationInterface) {
            return $this->patientRepo->findBy();
        }

        return $this->patientRepo->find($uriVariables['id']);
    }
}