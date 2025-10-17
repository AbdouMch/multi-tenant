<?php

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute(Attribute::TARGET_CLASS)]
final class EntityExists extends Constraint
{
    public string $message = 'An entity with the same values "{{ fields }}" already exists in the database.';

    public function __construct(
        public string $entityFQCN,
        public array $fields = ['id'],
        public string $entityManagerName = 'default',
        public ?string $tenantId = null,
        ?array $groups = null,
        mixed $payload = null
    ) {
        parent::__construct([], $groups, $payload);
    }

    public function validatedBy(): string
    {
        return EntityExistsValidator::class;
    }

    public function getTargets(): string
    {
        return Constraint::CLASS_CONSTRAINT;
    }
}
