<?php

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;

require_once 'vendor/autoload.php';

dd(strlen('Iywt7Je6R10hKlCkmSvuAD'));
#[Attribute(Attribute::TARGET_PROPERTY)]
final class GeneratedPublicId
{
    public function __construct(
        public readonly int $length = 22,
    ) {
    }
}

#[Entity]
class A {
    #[Column]
    private string $name;
    #[GeneratedPublicId]
    private int $publicId;
}

$a = new A();

$reflexion = new ReflectionClass($a);
dd($reflexion->getAttributes());

