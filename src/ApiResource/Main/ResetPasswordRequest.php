<?php

namespace App\ApiResource\Main;

use Symfony\Component\Validator\Constraints as Assert;

readonly class ResetPasswordRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Email]
        public string $email,
    ) {
    }
}