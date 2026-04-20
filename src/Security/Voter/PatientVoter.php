<?php

namespace App\Security\Voter;

use App\Entity\Main\Establishment;
use App\Entity\Main\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class PatientVoter extends Voter
{
    public const string VIEW = 'PATIENT_VIEW';

    public function __construct(
        private readonly AuthorizationCheckerInterface $authChecker,
    ) {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::VIEW && $subject instanceof Establishment;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        dd($user);
        if (!$user instanceof User) {
            return false;
        }

        /** @var Establishment $establishment */
        $establishment = $subject;

        // Platform-level admins can access any establishment's patients.
        if ($this->authChecker->isGranted('ROLE_SUPER_ADMIN')) {
            return true;
        }

        // ROLE_PATIENT_READ_ALL is held by ROLE_TENANT_ADMIN (via ROLE_PATIENT_ALL).
        // It grants access to all patients within the user's own establishment only.
        if ($this->authChecker->isGranted('ROLE_PATIENT_READ_ALL')) {
            return $user->getEstablishment()?->getId() === $establishment->getId();
        }

        // ROLE_PATIENT_READ (per-service granularity) is not yet implemented.
        return false;
    }
}