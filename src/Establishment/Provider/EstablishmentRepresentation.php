<?php

namespace App\Establishment\Provider;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Establishment\Dto\Establishment;
use App\Repository\Main\EstablishmentRepository;

class EstablishmentRepresentation implements ProviderInterface
{
    public function __construct(
        private readonly EstablishmentRepository $establishmentRepo,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        dump($operation, $uriVariables, $context);

        if ($operation instanceof CollectionOperationInterface) {
            return $this->establishmentRepo->findEstablishments();
        }

        return $this->establishmentRepo->findEstablishment($uriVariables['id']);
    }
}