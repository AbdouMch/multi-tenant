<?php

namespace App\State\Main;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Main\Establishment;
use App\ApiResource\Main\NewEstablishment;
use App\Entity\Main\Establishment as EstablishmentEntity;
use App\Entity\Main\User;
use App\Factory\TenantDbConfigFactory;
use App\Security\PasswordResetSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @implements ProcessorInterface<NewEstablishment, Establishment>
 */
class EstablishmentStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly PasswordResetSender $passwordResetSender,
        private readonly SluggerInterface $slugger,
    )
    {
    }

    /**
     * @param NewEstablishment $data
     */
    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Establishment
    {
        /** @var EstablishmentEntity $establishment */
        $establishment =  $this->em->wrapInTransaction(function () use ($data): EstablishmentEntity {
            $establishmentName = $data->getName();

            $dbName = $this->slugger->slug($establishmentName, '_')->lower()->toString();
            $dbConfig = TenantDbConfigFactory::createOne([
                'dbName' => $dbName,
                'dbUserName' => $dbName.'_tenant_user',
                'dbPassword' => bin2hex(random_bytes(12)),
            ]);

            $user = new User();
            $user->setEmail($data->getUser()->getEmail());
            $user->setPassword($this->passwordHasher->hashPassword($user, bin2hex(random_bytes(12))));
            $user->setRoles([User::ROLE_TENANT_ADMIN]);

            $this->em->persist($user);

            $establishment = new EstablishmentEntity();
            $establishment
                ->setName($establishmentName)
                ->setAddress($data->getAddress())
                ->addUser($user)
                ->setTenantId($dbConfig->getId())
            ;

            $this->em->persist($establishment);
            $this->em->flush();

            $this->passwordResetSender->sendFirstTimeCreation($user);

            return $establishment;
        });

        if (null === $establishment) {
            throw new HttpException(Response::HTTP_UNPROCESSABLE_ENTITY, 'Cannot create establishment');
        }

        return new Establishment(
            $establishment->getPublicId(),
            $establishment->getName(),
            $establishment->getAddress(),
        );
    }
}
