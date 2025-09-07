<?php

namespace App;

use Symfony\Component\String\ByteString;

class TenantDbPasswordGenerator
{
    public function generate(): string
    {
        return ByteString::fromRandom(32)->toString();
    }
}