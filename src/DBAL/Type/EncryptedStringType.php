<?php

namespace App\DBAL\Type;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

class EncryptedStringType extends StringType
{
    private static string $dbSecretKey;
    private static Key $key;

    public static function setDbSecretKey(string $dbSecretKey): void
    {
        self::$dbSecretKey = $dbSecretKey;
    }

    private function getKey(): Key
    {
        if (isset(self::$key)) {
            return self::$key;
        }

        if (isset(self::$dbSecretKey)) {
            self::$key = Key::loadFromAsciiSafeString(self::$dbSecretKey);

            return self::$key;
        }

        throw new \RuntimeException('Encryption key not initialized: insure that the key is injected by calling the static method setDbSecretKey()');
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return Crypto::decrypt($value, $this->getKey());
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return Crypto::encrypt($value, $this->getKey());
    }
}