<?php

namespace App\Tenant;

use App\ApiResource\Main\NewEstablishment;
use App\Entity\Main\Establishment as EstablishmentEntity;
use App\Entity\Main\TenantDbConfig;
use App\Entity\Main\User;

class EstablishmentFactory
{
    public function create(NewEstablishment $data, TenantDbConfig $dbConfig, User $admin): EstablishmentEntity
    {
        $establishment = new EstablishmentEntity();
        $establishment
            ->setName($data->name)
            ->setAddress($data->address)
            ->addUser($admin)
            ->setTenantId((string) $dbConfig->getId())
        ;

        return $establishment;
    }
}
