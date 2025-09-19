<?php

namespace App\DBAL\Mapping;

/**
 * Generates opaque public IDs for entities.
 *
 * Two entropy levels:
 * - Pass moreEntropy = false to use DEFAULT_LENGTH (12 chars, 72 bits)
 *   50% collision probability at ~68B rows
 * - Pass moreEntropy = true to use MORE_ENTROPY_LENGTH (16 chars, 96 bits)
 *   50% collision probability at ~281T rows
 */
class PublicIdGenerator
{
    /**
     * 72 bits
     * 50% chance of collision at 68 billion (~2³⁶) rows
     */
    public const DEFAULT_LENGTH = 12;
    /**
     * 96 bits
     * 50% chance of collision at ~281 trillion (~2^48) rows
     */
    public const MORE_ENTROPY_LENGTH = 16;


    public static function getLength(bool $moreEntropy): int
    {
        return $moreEntropy ? self::MORE_ENTROPY_LENGTH : self::DEFAULT_LENGTH;
    }

    public static function generate(int $length): string
    {
        $bytes = ceil($length * 3 / 4);

        return substr(strtr(base64_encode(random_bytes($bytes)), '+/', '-_'), 0, $length);
    }
}