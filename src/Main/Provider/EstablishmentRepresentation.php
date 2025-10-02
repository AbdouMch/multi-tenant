<?php

namespace App\Main\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Establishment\Establishment;
use App\Main\Repository\EstablishmentRepository;

class EstablishmentRepresentation implements ProviderInterface
{
    public function __construct(
        private readonly EstablishmentRepository $establishmentRepo,
    )
    {
    }

    /**
     * @return Establishment|Establishment[]|null
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if ($operation instanceof CollectionOperationInterface) {
            return $this->establishmentRepo->findBy();
        }

        return $this->establishmentRepo->find($uriVariables['id']);
    }
}