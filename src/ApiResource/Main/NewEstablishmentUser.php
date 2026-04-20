<?php

namespace App\ApiResource\Main;

use App\Entity\Main\User;
use App\Validator\EntityExists;
use Symfony\Component\Validator\Constraints as Assert;

#[EntityExists(entityFQCN: User::class, fields: ['email'])]
readonly class NewEstablishmentUser
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
    ) {
    }
}
