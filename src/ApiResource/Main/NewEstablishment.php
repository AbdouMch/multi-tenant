<?php

namespace App\ApiResource\Main;

use App\Entity\Main\Establishment as EstablishmentEntity;
use App\Validator\EntityExists;
use Symfony\Component\Validator\Constraints as Assert;

#[EntityExists(entityFQCN: EstablishmentEntity::class, fields: ["name", "address"])]
class NewEstablishment
{
    #[Assert\Length(min: 5, max: 255)]
    private string $name;

    #[Assert\Length(min: 10, max: 255)]
    private string $address;

    #[Assert\Valid]
    private NewEstablishmentUser $user;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function getUser(): NewEstablishmentUser
    {
        return $this->user;
    }

    public function setUser(NewEstablishmentUser $user): void
    {
        $this->user = $user;
    }
}