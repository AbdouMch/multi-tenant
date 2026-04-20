<?php

namespace App\Message;

final class CreateTenantDbMessage
{
    public function __construct(
        public readonly string $dbId,
    )
    {
    }
}
