<?php

namespace App\State\Main;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Main\Establishment;
use App\ApiResource\Main\UpdateEstablishment;
use App\Repository\Main\EstablishmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @implements ProcessorInterface<UpdateEstablishment, Establishment>
 */
class UpdateEstablishmentStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface  $em,
        private readonly EstablishmentRepository $establishmentRepo,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Establishment
    {
        if (!$data instanceof UpdateEstablishment || !$operation instanceof HttpOperation || 'PATCH' !== $operation->getMethod()) {
            throw new BadRequestHttpException('This processor only handles PATCH requests with an UpdateEstablishment input.');
        }

        $entity = $this->establishmentRepo->findOneBy(['publicId' => $uriVariables['publicId']]);

        if (null === $entity) {
            throw new NotFoundHttpException(sprintf('Establishment "%s" not found.', $uriVariables['publicId']));
        }

        if (null !== $data->name) {
            $entity->setName($data->name);
        }

        if (null !== $data->address) {
            $entity->setAddress($data->address);
        }

        $this->em->flush();

        return new Establishment(
            $entity->getPublicId(),
            $entity->getName(),
            $entity->getAddress(),
        );
    }
}
