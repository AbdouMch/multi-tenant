# 🎓 Introduction to Multi-tenant architecture
---

Welcome to the Symfony and muti-tenant architecture!  
This project is based on the tutorial [Implement Multi Tenant Architecture in Symfony](https://dev.to/tbeaumont79/implement-multi-tenant-architecture-in-symfony-4l1l).

See also : 
- [Multi-Tenancy Architecture - System Design](https://www.geeksforgeeks.org/system-design/multi-tenancy-architecture-system-design/?utm_source=chatgpt.com)
- [Encryption](https://github.com/defuse/php-encryption/blob/master/docs/Tutorial.md)

## ✅ Requirements

You can run the app **using Docker (recommended)** or set it up **locally on your machine**.

### 🔧 Docker Setup (Recommended)

Make sure the following are installed:

- Docker
- Docker Compose
- GNU Make
- WSL (for Windows users)

### 💻 Local Setup (Without Docker)

You’ll need:

- PHP >= 8.1
- Composer
- Symfony CLI
à)"e

---

## 🚀 Installation

### 📦 Docker Setup

Run the following command from the project root:

```bash
make init
```

### 🛠 Local Installation (Without Docker)

* Install dependencies

```bash
composer install
```

* Configure environment variables

* Create a file named .env.local at the root of the project based on the .env-sample file:

* Replace db_user, db_password, and symfony_app with your local MySQL credentials.
```dotenv
DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/symfony_app?serverVersion=8.0"
```

* Create the database

```bash
php bin/console doctrine:database:create
```

* Run database migrations

```bash
php bin/console doctrine:migrations:migrate
```

* Load fixtures (optional, if provided)

```bash
php bin/console doctrine:fixtures:load
```

* Start the Symfony local server

```bash
symfony serve -d
```

## Multi-tenant architecture :

It is a software architecture pattern where a single application instance serves multiple customers (tenants). 

A tenant is a groups of users (e.g a company, department or client) that shares common access to the software.
Each tenant's data is isolated and secured from others

### Types of multi-tenancy :

- Database per tenant: Each tenant gets a separate database. Strong isolation, but harder to scale with thousands of tenants.
- Schema per tenant: One database, multiple schemas (one per tenant). Balance between isolation and scalability.
- Shared schema: A single database and schema, but rows are tagged with a tenant_id. Most resource-efficient, 
but requires strong access control.

### Pros :

- **Low cost** : All users shares the same infrastructure that reduces operational consts
- **Scalability** : When scaling the application, it is easier than scaling multiple instances
- **Maintenance** : Updates and bug fixes are applied to all tenants simultaneously
- **Isolation and resource optimization** : Each tenant has access to their own data with a better resource utilisation 

### Design principles :
- **Data Isolation** :
  - Separation of tenant data : Use of separate databases, schemas or logical data partitions to ensure that each tenant's data
is isolated from others
  - Access control : Use role-based access control (RBAC) and tenant-specific permission to prevent unauthorized access to tenant data
- **Scalability** :
  - Elasticity : the system should be able to scale horizontally and vertically to handle an increasing number of tenants and users.
This includes auto-scaling for both application and database layers.
  - Resource allocation : Allocate resources dynamically based on tenant  demand to optimize performance and cost.
Use containerization to manage resources efficiently.
- **Performance isolation** :
  - Resource limits : Use mechanisms such as quotas, throttling, and resource limiting to prevent one tenant's heavy usage
from affecting the performance of others.
  - Load balancing : Distribute workloads evenly across servers and services to prevent bottlenecks and ensure consistent performance
- **Security** :
  - Data encryption : Encrypt Tenant's sensitive data both at rest and in transit to protect against unauthorized access and breaches
  - Activity Auditing : Audit tenant activity for security anomalies and potential threats

### Core components :
1. **Tenant Management** :
  - Tenant Provisioning : Automate the creation and configuration of tenant environments. This includes setting up databases,
allocating resources, and initializing tenant-specific configuration
  - Tenant Isolation ; Ensures data and configuration isolation between tenants by using different techniques like separate
databases, schemas or logical partitions.
  - Tenant Metadata : Stores metadata about each tenant, including configuration settings, ressource allocations, and usage statistics.
2. **Authentication and Authorization** :
  - Identity management : Manage user identities and authentication across tenants.
  - Access control : RBAC to manage user permissions within and across tenants

### Performance optimizations :
1. Resource Management :
   - Dynamic Resource Allocation
   - Dynamic Resource Allocation
2. Caching Strategies :
   - Tenant-Specific Caches: Use separate caches for each tenant to isolate their data.
   - Distributed Caching: Implement distributed caching systems like Redis or Memcached to store frequently accessed data
closer to the application, reducing database load.
3. Load Balancing : 
   - Even Traffic Distribution by load balancers
   - Geographical Load Balancing : Distribute traffic based on geographical location
4. Database Optimization :
   - Indexing
   - Sharding
   - Connection Pooling : To reduce the overhead of establishing connections repeatedly 
   - Efficient Query Design
   - Read Replicas instead of primary database
5. Application-Level Optimization:
   - Asynchronous Processing
   - Microservices Architecture

## Useful commands :

### MySQL :
- Create a new user : 
```mysql
CREATE USER 'tenant_admin'@'%' IDENTIFIED BY 'StrongPasswordHere!';
```
- Delete a user :
```mysql
DROP USER 'username'@'host';
```
- Grant access :
```mysql
GRANT CREATE, DROP, ALTER, INDEX, INSERT, UPDATE, DELETE, SELECT, REFERENCES, CREATE TEMPORARY TABLES, LOCK TABLES, EXECUTE ON *.* TO 'tenant_admin'@'%';
FLUSH PRIVILEGES;
```
  - for dev/test
```mysql
GRANT ALL PRIVILEGES ON *.* TO 'tenant_admin'@'%' WITH GRANT OPTION;
FLUSH PRIVILEGES;
```

### Symfony Cli:
#### Managing secrets : 
- List all secrets with reveals
```shell
php bin/console secrets:list --reaveal
```
- Add new secret
```shell
php bin/console secrets:set <secret-name>
```
- Remove a secret
```shell
php bin/console secrets:remove <secret-name>
```
#### Installing Lexik-JWT-Auth-Bundle
- Generate passphrase : 
```shell
php -r "echo bin2hex(random_bytes(32)), PHP_EOL;"
```
- Generate pair key :
```shell
php bin/console lexik:jwt:generate-keypair
```
#### Managing DBs
- Run main database migrations : 
```shell
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate
```
- load fixture to the main DB :
```shell
php bin/console d:f:l --group=main
```
- create tenant DB:
```shell
php bin/console tenant:database:create
```
- Run migration on tenant DB:
```shell
php tenant:migrations:diff <dbId>
php tenant:migrations:migrate init
```
- Load tenant fixtures:
```shell
php bin/console tenant:fixtures:load <dbId>
```
## Tutorial

### Design phases :
- 2 namespaces for entities : 
  - Main : all entities are persisted in the main database