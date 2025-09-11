<?php

namespace App\Repository\Main;

use App\Entity\Main\TenantDbConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TenantDbConfig>
 *
 *
 */
class TenantDbConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TenantDbConfig::class);
    }
}
