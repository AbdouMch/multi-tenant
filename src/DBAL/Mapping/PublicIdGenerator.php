<?php

namespace App\DBAL\Mapping;

class PublicIdGenerator
{
    public static function generate(int $length = 22): string
    {
        $bytes = ceil($length * 3 / 4);

        return substr(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), 0, $length);
    }
}