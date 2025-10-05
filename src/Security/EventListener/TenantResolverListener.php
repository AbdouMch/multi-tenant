<?php

namespace App\Security\EventListener;

use App\Entity\Main\User;
use App\Security\TenantContext;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\Security\Core\Event\AuthenticationSuccessEvent;

final class TenantResolverListener
{
    public function __construct(
        private readonly TenantContext $tenantContext,
    )
    {
    }

    #[AsEventListener(event: 'security.authentication.success')]
    public function onSecurityAuthenticationSuccess(AuthenticationSuccessEvent $event): void
    {
        $token = $event->getAuthenticationToken();
        $user = $token->getUser();

        if ($user instanceof User && null !== ($establishment = $user->getEstablishment())) {
            $this->tenantContext->setTenantId($establishment->getTenantId());
        }
    }
}
