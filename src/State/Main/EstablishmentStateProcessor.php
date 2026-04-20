<?php

namespace App\State\Main;

use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\ApiResource\Main\Establishment;
use App\ApiResource\Main\NewEstablishment;
use App\Entity\Main\Establishment as EstablishmentEntity;
use App\Entity\Main\User;
use App\Security\PasswordResetSender;
use App\Service\TenantProvisioningService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

/**
 * @implements ProcessorInterface<NewEstablishment, Establishment>
 */
class EstablishmentStateProcessor implements ProcessorInterface
{
    public function __construct(
        private readonly EntityManagerInterface      $em,
        private readonly UserPasswordHasherInterface $passwordHasher,
        private readonly PasswordResetSender         $passwordResetSender,
        private readonly SluggerInterface            $slugger,
        private readonly TenantProvisioningService   $tenantProvisioningService,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): Establishment
    {
        if (!$data instanceof NewEstablishment || !$operation instanceof HttpOperation || 'POST' !== $operation->getMethod()) {
            throw new BadRequestHttpException('This processor only handles POST requests with a NewEstablishment input.');
        }

        // Capture for use after the transaction.
        $dbConfig     = null;
        $createdUser  = null;

        $establishment = $this->em->wrapInTransaction(
            function () use ($data, &$dbConfig, &$createdUser): EstablishmentEntity {
                $dbName   = $this->slugger->slug($data->name, '_')->lower()->toString();
                $dbConfig = $this->tenantProvisioningService->buildDbConfig($dbName);
                $this->em->persist($dbConfig);

                $createdUser = new User();
                $createdUser->setEmail($data->user->email);
                $createdUser->setPassword(
                    $this->passwordHasher->hashPassword($createdUser, bin2hex(random_bytes(32)))
                );
                $createdUser->setRoles([User::ROLE_TENANT_ADMIN]);
                $this->em->persist($createdUser);

                $establishment = new EstablishmentEntity();
                $establishment
                    ->setName($data->name)
                    ->setAddress($data->address)
                    ->addUser($createdUser)
                    ->setTenantId((string) $dbConfig->getId())
                ;
                $this->em->persist($establishment);

                return $establishment;
            }
        );

        // Dispatch outside the transaction: the handler must read the committed record.
        $this->tenantProvisioningService->dispatchProvisioning($dbConfig);

        // Send the first-login password-reset link.
        $this->passwordResetSender->sendFirstTimeCreation($createdUser);

        return new Establishment(
            $establishment->getPublicId(),
            $establishment->getName(),
            $establishment->getAddress(),
        );
    }
}
