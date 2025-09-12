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
                            'GeneratedPublicId requires a mapped Doctrine field. Property %s::%s is not mapped.',
                            $classMetadata->getName(),
                            $propertyName
                        ));
                    }

                    $fieldMapping = $classMetadata->getFieldMapping($propertyName);

                    if (Types::STRING !== $fieldMapping->type) {
                        throw new \InvalidArgumentException(sprintf(
                            'GeneratedPublicId requires column type (%s). Found column type (%s) for %s::%s',
                            Types::STRING,
                            $fieldMapping->type,
                            $classMetadata->getName(),
                            $propertyName
                        ));
                    }

                    $attributeInstance = $attribute->newInstance();

                    if ($attributeInstance->length > $fieldMapping['length']) {
                        throw new \InvalidArgumentException(sprintf(
                            'GeneratedPublicId length (%d) exceeds column length (%d) for %s::%s',
                            $attributeInstance->length,
                            $fieldMapping['length'],
                            $classMetadata->getName(),
                            $propertyName
                        ));
                    }

                    self::$configurations[$classMetadata->getName()][$propertyName] = [
                        'length' => $attributeInstance->length,
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