<?php

namespace App\ApiResource\Main;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateEstablishment
{
    #[Assert\Length(min: 5, max: 255)]
    public ?string $name = null;

    #[Assert\Length(min: 10, max: 255)]
    public ?string $address = null;
}
