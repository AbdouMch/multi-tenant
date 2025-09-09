<?php

namespace App\DBAL;

use Doctrine\DBAL\Tools\DsnParser;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\DependencyInjection\Attribute\AsDecorator;

#[AsDecorator(decorates: 'doctrine.dbal.connection_factory')]
class ConnectionFactory extends \Doctrine\Bundle\DoctrineBundle\ConnectionFactory
{
    public function __construct(
        array $typesConfig = [],
        ?DsnParser $dsnParser = null
    )
    {
        parent::__construct($typesConfig, $dsnParser);
    }

    public function addTypes(array $types): void
    {
        foreach ($types as $name => $type) {
            Type::addType($name, $type);
        }
    }
}