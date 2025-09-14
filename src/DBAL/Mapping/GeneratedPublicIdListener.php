<?php

namespace App\DBAL\Mapping;

use App\DBAL\Type\GeneratedPublicIdType;
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

                    if (GeneratedPublicIdType::NAME !== $fieldMapping->type) {
                        throw new \InvalidArgumentException(sprintf(
                            'GeneratedPublicId requires column type (%s). Found column type (%s) for %s::%s',
                            Types::STRING,
                            $fieldMapping->type,
                            $classMetadata->getName(),
                            $propertyName
                        ));
                    }

                    $length = $fieldMapping['length'];

                    if (isset($length) && PublicIdGenerator::DEFAULT_LENGTH !== $length && PublicIdGenerator::MORE_ENTROPY_LENGTH !== $length) {
                        throw new \InvalidArgumentException(sprintf(
                            '%s Type length should be (%d) or (%d) for %s::%s. Found column length (%d). See %s for more information.',
                            GeneratedPublicIdType::NAME,
                            PublicIdGenerator::DEFAULT_LENGTH,
                            PublicIdGenerator::MORE_ENTROPY_LENGTH,
                            $classMetadata->getName(),
                            $propertyName,
                            $length,
                            PublicIdGenerator::class
                        ));
                    }

                    if (isset($fieldMapping['options']['moreEntropy']) && false === is_bool($fieldMapping['options']['moreEntropy'])) {
                        throw new \InvalidArgumentException(sprintf(
                            '%s $options["moreEntropy"] should be of type bool for property "%s" in %s. %s given',
                            GeneratedPublicIdType::NAME,
                            $propertyName,
                            $classMetadata->getName(),
                            gettype($fieldMapping['options']['moreEntropy'])
                        ));
                    }

                    if (!$length) {
                        $moreEntropy = $fieldMapping['options']['moreEntropy'] ?? false;

                        $length = PublicIdGenerator::getLength($moreEntropy);
                    }

                    // force length for schema creation
                    $fieldMapping->length = $length;

                    // force uniqueness for schema creation
                    $fieldMapping->unique = true;

                    self::$configurations[$classMetadata->getName()][$propertyName] = [
                        'length' => $length,
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