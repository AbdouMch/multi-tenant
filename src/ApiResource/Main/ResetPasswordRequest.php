<?php

namespace App\ApiResource\Main;

use Symfony\Component\Validator\Constraints as Assert;

class ResetPasswordRequest
{
    #[Assert\Email]
    private string $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
}