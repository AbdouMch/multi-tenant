<?php

namespace App\ApiResource\Main;

use App\Entity\Main\Establishment as EstablishmentEntity;
use App\Validator\EntityExists;
use Symfony\Component\Validator\Constraints as Assert;

#[EntityExists(entityFQCN: EstablishmentEntity::class, fields: ['name', 'address'])]
readonly class NewEstablishment
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 5, max: 255)]
        public string $name,

        #[Assert\NotBlank]
        #[Assert\Length(min: 10, max: 255)]
        public string $address,

        #[Assert\Valid]
        public NewEstablishmentUser $user,
    ) {
    }
}
