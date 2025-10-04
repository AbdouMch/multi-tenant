<?php

namespace App\Doctrine\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use App\ApiResource\Tenant\Patient;
use App\Entity\Tenant\Patient as PatientEntity;
use App\Security\TenantContext;
use Doctrine\ORM\QueryBuilder;

class PatientSelect implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private readonly TenantContext          $tenantContext,
    )
    {
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $this->getCommonSelectQb($queryBuilder, $queryNameGenerator, $resourceClass, $operation, $context);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, ?Operation $operation = null, array $context = []): void
    {
        $this->getCommonSelectQb($queryBuilder,$queryNameGenerator, $resourceClass, $operation, $identifiers);
    }

    private function getCommonSelectQb(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        if (PatientEntity::class !== $resourceClass || Patient::class !== $operation->getClass()) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $tenantParam = $queryNameGenerator->generateParameterName('tenantId');
        $queryBuilder->setParameter($tenantParam, $this->tenantContext->getTenantPublicId());

        $queryBuilder->select(sprintf(
            'NEW %s(%s.publicId, %s.firstname, %s.lastname, %s.birthDate, %s.nir, :%s)',
            Patient::class,
            $rootAlias, $rootAlias, $rootAlias, $rootAlias, $rootAlias,
            $tenantParam
        ));;
    }
}