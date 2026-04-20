<?php

namespace App\Security\EventListener;

use App\Entity\Main\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class JwtCreatedListener
{
    #[AsEventListener(event: 'lexik_jwt_authentication.on_jwt_created')]
    public function onJWTCreated(JWTCreatedEvent $event): void
    {
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        // Embed the establishment's public ID so the client knows which tenant it
        // authenticated into. This is informational only — the server always resolves
        // the active tenant from the URL parameter, never from this claim.
        $payload = $event->getData();
        $payload['establishmentId'] = $user->getEstablishment()?->getPublicId();
        $event->setData($payload);
    }
}
