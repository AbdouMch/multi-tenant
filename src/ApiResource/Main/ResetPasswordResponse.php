<?php

namespace App\ApiResource\Main;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Post;
use App\ApiResource\Main\ResetPasswordRequest;
use App\State\Main\ResetPasswordStateProcessor;

#[ApiResource(
    shortName: 'Reset Password',
    operations: [
        new Post(
            uriTemplate: '/reset-password',
            input: ResetPasswordRequest::class,
            processor: ResetPasswordStateProcessor::class,
        )
    ]
)]
class ResetPasswordResponse
{
    public function __construct(
        public readonly string $message
    )
    {
    }
}