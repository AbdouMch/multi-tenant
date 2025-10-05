<?php

namespace App\Filter;

use ApiPlatform\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Metadata\Operation;
use Doctrine\ORM\QueryBuilder;

class PartialSearchFilter implements FilterInterface
{
    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, ?Operation $operation = null, array $context = []): void
    {
        $alias = $queryBuilder->getRootAliases()[0];

        foreach ($context['filters']['search'] ?? [] as $property => $value) {
            $parameterName = $queryNameGenerator->generateParameterName($property);

            $field = $alias . '.' . $property;


            $queryBuilder
                ->andWhere($queryBuilder->expr()->like('LOWER(' . $field . ')', ':' . $parameterName))
                ->setParameter($parameterName, '%' . strtolower($value) . '%');
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'search' => [
                'description' => 'example: search[:property]',
                'name' => 'Partials search filter',
                'is_collection' => true,
            ]
        ];
    }
}