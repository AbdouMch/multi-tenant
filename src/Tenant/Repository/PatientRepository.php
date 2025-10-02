<?php

namespace App\Tenant\Repository;

use App\ApiResource\Tenant\Patient;
use App\Entity\Tenant\Patient as PatientEntity;
use App\Security\TenantContext;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

class PatientRepository
{
    public function __construct(
        private readonly EntityManagerInterface $tenantEntityManager,
        private readonly TenantContext          $tenantContext,
    )
    {
    }

    public function find(string $publicId): ?Patient
    {
        $qb = $this->getCommonSelectQb()
            ->where('p.publicId = :publicId')
            ->setParameter('publicId', $publicId);

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * @return Patient[]
     */
    public function findBy(): array
    {
        return $this->getCommonSelectQb()
            ->getQuery()
            ->getResult();
    }

    private function getCommonSelectQb(): QueryBuilder
    {
        return $this->tenantEntityManager->createQueryBuilder()
            ->select(sprintf("NEW %s(p.publicId, p.firstname, p.lastname, p.birthDate, p.nir, :tenantId)", Patient::class))
            ->from(PatientEntity::class, 'p')
            ->setParameter(':tenantId', $this->tenantContext->getTenantPublicId())
            ;
    }
}