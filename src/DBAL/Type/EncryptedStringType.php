<?php

namespace App\DBAL\Type;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class EncryptedStringType extends StringType
{
    private Key $key;

    public function __construct(
        private readonly string $dbSecretKey
    )
    {
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): string
    {
        if (str_contains($value, '<ENC>')) {
            $secret = str_replace('<ENC>', '', $value);

            return Crypto::decrypt($secret, $this->getKey());
        }

        return $value;
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): string
    {
        if (str_contains($value, '<ENC>')) {
            return $value;
        }

        $ciphertext = Crypto::encrypt($value, $this->getKey());

        return $ciphertext . '<ENC>';
    }

    private function getKey(): Key
    {
        if (isset($this->key)) {
            return $this->key;
        }

        $this->key = Key::loadFromAsciiSafeString($this->dbSecretKey);

        return $this->key;
    }
}