<?php

namespace App\MessageHandler;

use App\Message\CreateTenantDbMessage;
use App\Repository\Main\TenantDbConfigRepository;
use App\Tenant\TenantDatabaseManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final class CreateTenantDbMessageHandler
{
    public function __construct(
        private readonly TenantDbConfigRepository $tenantDbConfigRepo,
        private readonly TenantDatabaseManager    $tenantDatabaseManager,
    ) {
    }

    public function __invoke(CreateTenantDbMessage $message): void
    {
        $dbConfig = $this->tenantDbConfigRepo->find($message->dbId);

        if (null === $dbConfig) {
            throw new \RuntimeException(sprintf('TenantDbConfig "%s" not found — cannot provision database.', $message->dbId));
        }

        $this->tenantDatabaseManager->createDatabase($dbConfig);
    }
}
