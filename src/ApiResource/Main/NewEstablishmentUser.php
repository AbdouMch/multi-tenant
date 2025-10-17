<?php

namespace App\ApiResource\Main;

use App\Entity\Main\User;
use App\Validator\EntityExists;
use Symfony\Component\Validator\Constraints as Assert;

#[EntityExists(entityFQCN: User::class, fields: ['email'])]
class NewEstablishmentUser
{
    #[Assert\Email]
    private string $email;

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}