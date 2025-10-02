<?php

namespace App\Main\Repository;

use App\ApiResource\Establishment\Establishment;
use App\Repository\Main\EstablishmentRepository as EstablishmentEntityRepo;

class EstablishmentRepository
{
    public function __construct(
       private readonly EstablishmentEntityRepo $entityRepo
    )
    {
    }

    public function find(string $publicId): ?Establishment
    {
        return $this->getCommonSelectQb()
            ->where('e.publicId = :publicId')
            ->setParameter('publicId', $publicId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return Establishment[]
     */
    public function findBy(): array
    {
        return $this->getCommonSelectQb()
            ->getQuery()
            ->getResult();
    }

    private function getCommonSelectQb(): \Doctrine\ORM\QueryBuilder
    {
        return $this->entityRepo->createQueryBuilder('e')
            ->select(sprintf('NEW %s(e.publicId, e.name, e.address)', Establishment::class));
    }
}