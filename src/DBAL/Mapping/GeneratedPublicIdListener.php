<?php

namespace App\DBAL\Mapping;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;

#[AsDoctrineListener(event: Events::loadClassMetadata)]
#[AsDoctrineListener(event: Events::prePersist)]
class GeneratedPublicIdListener
{
    /**
     * @var array<class-string, array<string, array{length: int}>>
     */
    protected static array $configurations = [];

    public function loadClassMetadata(LoadClassMetadataEventArgs $args): void
    {
        $classMetadata = $args->getClassMetadata();

        if (false === isset(self::$configurations[$classMetadata->getName()])) {
            $reflection = $classMetadata->getReflectionClass();

            foreach ($reflection->getProperties() as $property) {
                $attribute = $property->getAttributes(GeneratedPublicId::class)[0] ?? null;

                if (null !== $attribute) {
                    $propertyName = $property->getName();

                    if (false === $classMetadata->hasField($propertyName)) {
                        throw new \InvalidArgumentException(sprintf(
                            'Property %s::%s must be a mapped Doctrine field.',
                            $classMetadata->getName(),
                            $propertyName
                        ));
                    }

                    $fieldMapping = $classMetadata->getFieldMapping($propertyName);

                    if (Types::STRING !== $fieldMapping->type) {
                        throw new \InvalidArgumentException(sprintf(
                            'Property %s::%s must use type "%s". Found "%s".',
                            $classMetadata->getName(),
                            $propertyName,
                            Types::STRING,
                            $fieldMapping->type
                        ));
                    }

                    $length = $fieldMapping['length'];

                    if (isset($length) && PublicIdGenerator::DEFAULT_LENGTH !== $length && PublicIdGenerator::MORE_ENTROPY_LENGTH !== $length) {
                        throw new \InvalidArgumentException(sprintf(
                            'Property %s::%s length must be %d or %d. Found %d.',
                            $classMetadata->getName(),
                            $propertyName,
                            PublicIdGenerator::DEFAULT_LENGTH,
                            PublicIdGenerator::MORE_ENTROPY_LENGTH,
                            $length
                        ));
                    }

                    /** @var GeneratedPublicId $attributeInstance */
                    $attributeInstance = $attribute->newInstance();
                    $validLength = $attributeInstance->length ?? PublicIdGenerator::getLength($attributeInstance->moreEntropy);

                    if (null !== $length && $validLength !== $length) {
                        throw new \InvalidArgumentException(sprintf(
                            'Property %s::%s length does not match %s(moreEntropy=%s).',
                            $classMetadata->getName(),
                            $propertyName,
                            GeneratedPublicId::class,
                            $attributeInstance->moreEntropy ? 'true' : 'false'
                        ));
                    }

                    // force length for schema creation
                    $fieldMapping->length = $validLength;

                    // force uniqueness for schema creation
                    $fieldMapping->unique = true;

                    self::$configurations[$classMetadata->getName()][$propertyName] = [
                        'length' => $validLength,
                    ];
                }
            }
        }
    }

    public function prePersist(PrePersistEventArgs $args): void
    {
        $om = $args->getObjectManager();
        $entity = $args->getObject();

        $classMetadata = $om->getClassMetadata(get_class($entity));

        if (isset(self::$configurations[$classMetadata->getName()])) {
            $entityConfiguration = self::$configurations[$classMetadata->getName()];
            foreach ($entityConfiguration as $propertyName => $config) {
                $propertyAccessor = $classMetadata->getPropertyAccessor($propertyName);

                if (null === $propertyAccessor->getValue($entity)) {
                    $propertyAccessor->setValue($entity, PublicIdGenerator::generate($config['length']));
                }
            }
        }
    }
}