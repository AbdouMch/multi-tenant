<?php

namespace App\Repository\Main;

use App\Entity\Main\Establishment;
use App\Establishment\Dto\Establishment as EstablishmentDto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Establishment>
 */
class EstablishmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Establishment::class);
    }

    public function findEstablishment(int $id)
    {
        return $this->createQueryBuilder('e')
            ->select(sprintf('NEW %s(e.publicId, e.name, e.address)', EstablishmentDto::class))
            ->where('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getResult();
    }

    public function findEstablishments()
    {
        return $this->createQueryBuilder('e')
            ->select(sprintf('NEW %s(e.publicId, e.name, e.address)', EstablishmentDto::class))
            ->getQuery()
            ->getResult();
    }
}
