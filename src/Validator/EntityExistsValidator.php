<?php

namespace App\Validator;

use App\Security\TenantContext;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class EntityExistsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly ManagerRegistry $managerRegistry,
        private readonly PropertyAccessorInterface $propertyAccessor,
        private readonly TenantContext     $tenantContext,
    )
    {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (null === $value || '' === $value) {
            return;
        }

        $em = $this->managerRegistry->getManager($constraint->entityManagerName);

        if (null !== $constraint->tenantId) {
            $this->tenantContext->setTenantId($constraint->tenantId);
        }

        $repository = $em->getRepository($constraint->entityFQCN);
        $criteria = [];

        foreach ($constraint->fields as $field) {
            $criteria[$field] = $this->propertyAccessor->getValue($value, $field);
        }

        $entity = $repository->findOneBy($criteria);

        if (null !== $entity) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ fields }}', implode(', ', $constraint->fields))
                ->addViolation()
            ;
        }
    }
}
