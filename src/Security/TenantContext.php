<?php

namespace App\Security;

use App\Repository\Main\EstablishmentRepository;
use Hakam\MultiTenancyBundle\Event\SwitchDbEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TenantContext
{
    private ?string $tenantId = null;
    private ?string $tenantPublicId = null;

    public function __construct(
        private readonly EstablishmentRepository  $establishmentRepository,
        private readonly EventDispatcherInterface $dispatcher
    )
    {
    }

    public function setTenantId(string $tenantId): void
    {
        $establishment = $this->establishmentRepository->findOneBy([
            'tenantId' => $tenantId,
        ]);

        if (null === $establishment) {
            throw new \InvalidArgumentException('Invalid tenant id');
        }

        $this->tenantId = $establishment->getTenantId();
        $this->tenantPublicId = $establishment->getPublicId();

        $this->dispatcher->dispatch(new SwitchDbEvent($this->tenantId));
    }

    public function setTenantIdByPublicId(string $publicId): void
    {
        $establishment = $this->establishmentRepository->findOneBy(['publicId' => $publicId]);

        if (null === $establishment) {
            throw new \InvalidArgumentException('Invalid tenant id');
        }

        $this->tenantId = $establishment->getTenantId();
        $this->tenantPublicId = $establishment->getPublicId();

        $this->dispatcher->dispatch(new SwitchDbEvent($this->tenantId));
    }

    public function getTenantId(): ?string
    {
        return $this->tenantId;
    }

    public function getTenantPublicId(): ?string
    {
        return $this->tenantPublicId;
    }

    public function hasTenant(): bool
    {
        return null !== $this->tenantId;
    }
}