<?php

namespace App\Provider\Tenant;

use ApiPlatform\Metadata\CollectionOperationInterface;
use ApiPlatform\Metadata\HttpOperation;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Tenant\Patient;
use App\Security\TenantContext;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProviderInterface<Patient[]|Patient|null>
 */
class PatientProvider implements ProviderInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.item_provider')]
        private readonly ProviderInterface $itemProvider,
        #[Autowire(service: 'api_platform.doctrine.orm.state.collection_provider')]
        private readonly ProviderInterface $collectionProvider,
        private readonly TenantContext     $tenantContext,
    )
    {
    }

    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $tenantId = $uriVariables['tenantId'] ?? null;

        if (null !== $tenantId) {
            $this->tenantContext->setTenantIdByPublicId($tenantId);
        }

        if (false === $this->tenantContext->hasTenant()) {
            throw new \LogicException('Unable to detect the tenant id.');
        }

        unset($uriVariables['tenantId'], $context['uri_variables']['tenantId']);

        if (isset($uriVariables['id'])) {
            $uriVariables['publicId'] = $uriVariables['id'];
            unset($uriVariables['id']);
        }

        if (isset($context['uri_variables']['id'])) {
            $context['uri_variables']['publicId'] = $context['uri_variables']['id'];
            unset($context['uri_variables']['id']);
        }

        if ($operation instanceof HttpOperation) {
            $operationUriVariables = $operation->getUriVariables();
            unset($operationUriVariables['tenantId']);

            if (isset($operationUriVariables['id'])) {
                /** @var Link $idLink */
                $idLink = $operationUriVariables['id']->withIdentifiers(['publicId']);
                unset($operationUriVariables['id']);
                $operationUriVariables['publicId'] = $idLink;
            }

            $operation = $operation->withUriVariables($operationUriVariables);
            $context['operation'] = $operation;
        }

        if ($operation instanceof CollectionOperationInterface) {
            return $this->collectionProvider->provide($operation, $uriVariables, $context);
        }

        return $this->itemProvider->provide($operation, $uriVariables, $context);
    }
}