<?php

namespace App\Security;

use App\Entity\Main\User;
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

//    #[AsEventListener(event: 'kernel.request', priority: 0)]
//    public function onRequestEvent(RequestEvent $event): void
//    {
//        $reqParameters = $event->getRequest()->attributes;
//
//        if ($reqParameters->get('tenant_aware', false)) {
//            $tenantIdUriVariable = $reqParameters->get('tenantIdUriVariable', 'tenantId');
//            $publicId = $reqParameters->get($tenantIdUriVariable);
//
//            if (null === $publicId) {
//                throw new \InvalidArgumentException('Tenant id must be provided');
//            }
//
//            $this->tenantContext->setTenantIdByPublicId($publicId);
//        }
//    }
}
