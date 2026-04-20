# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
make init          # Full setup: start containers, install deps, run migrations & fixtures
make up            # Start Docker containers
make vendor        # composer install inside PHP container
make db-migrate    # Run Doctrine migrations on main DB
make db-fixtures   # Load fixtures
make stan          # PHPStan static analysis on changed PHP files
make cs-fix        # PHP-CS-Fixer on changed PHP files
make sf c=<cmd>    # Run any Symfony console command (e.g. make sf c=about)
make php           # Shell into PHP container
make db            # Shell into MySQL container
make logs          # Follow Docker logs
```

Run tests: `phpunit` (config in `phpunit.dist.xml`)

Generate JWT keys: `make sf c="lexik:jwt:generate-keypair"`

## Architecture: Database-per-Tenant Multi-Tenancy

This is a medical SaaS app where each **Establishment** (tenant) gets its own isolated database. The [hakam/multi-tenancy-bundle](https://github.com/hakam/multi-tenancy-bundle) handles Doctrine connection switching.

### Two Doctrine Entity Managers

| Manager | Database | Entity namespaces |
|---------|----------|-------------------|
| `default` | `DATABASE_URL` (main) | `App\Entity\Main`, `App\Entity\Loggable` |
| `tenant` | `TENANT_DEFAULT_DATABASE_URL` | `App\Entity\Tenant`, `App\Entity\Loggable` |

Always inject the correct manager. For tenant entities use `@ORM\Entity` with the `tenant` manager, and set migrations in `migrations/Tenant/` (`DoctrineMigrations\Tenant` namespace). Main DB migrations go in `migrations/Main/`.

### Tenant Switching Flow

1. API request URL contains `{publicId}` (obfuscated ID, not internal auto-increment)
2. `TenantContext::setTenantIdByPublicId()` resolves the `Establishment` → internal `tenantId`
3. Dispatches `SwitchDbEvent($tenantId)` → hakam bundle switches the `tenant` Doctrine connection
4. All subsequent tenant entity queries use that tenant's database

The `TenantDatabaseManager` (`src/Tenant/`) provisions new tenant databases (create DB, user, grant permissions). This is triggered asynchronously via `CreateTenantDbMessage` / Symfony Messenger.

### Public IDs

Entities expose `publicId` (UUID-like) instead of internal `id` for all API routes. A custom `GeneratedPublicId` attribute + event listener auto-generates these. API Platform resources use `publicId` as the URI variable (`/establishments/{publicId}`).

### Encrypted Tenant Credentials

`TenantDbConfig` stores `dbName`, `dbUserName`, `dbPassword`, `dbHost`, `dbPort` using a custom `encrypted_string` DBAL type (defuse/php-encryption). The encryption key comes from `DB_SECRET_KEY` env var, set in `Kernel::boot()`.

### API Platform Patterns

- **DTOs** in `src/ApiResource/` decouple HTTP contracts from entities (e.g., `NewEstablishment`, `UpdateEstablishment` are separate from the `Establishment` entity)
- **State Processors** in `src/State/` handle POST/PATCH logic
- **Query Extensions** in `src/Doctrine/Extension/` handle filtering/access control (e.g., `PatientSelect`, `EstablishmentSelect`)

### Audit Logging

Gedmo Loggable is active on both entity managers. Changes to `Establishment`, `User`, `Patient`, etc. are tracked in the `LogEntry` table.

### Async Messaging

Messenger uses a Doctrine transport (`doctrine://default?auto_setup=0`). The `CreateTenantDbMessage` is dispatched on establishment creation and handled by `CreateTenantDbMessageHandler`.

## Key Environment Variables

```
DATABASE_URL                 # Main DB (establishments, users, etc.)
TENANT_DEFAULT_DATABASE_URL  # Template connection for tenant DBs
DB_SECRET_KEY                # Encryption key for TenantDbConfig fields
JWT_SECRET_KEY / JWT_PUBLIC_KEY / JWT_PASSPHRASE
MESSENGER_TRANSPORT_DSN      # doctrine://default
```

## Security

JWT authentication via `lexik/jwt-authentication-bundle`. Roles: `ROLE_ADMIN`, `ROLE_TENANT_ADMIN`, `ROLE_USER`. API Platform resources are secured with `is_granted()` in their `#[ApiResource]` attributes.