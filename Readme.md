# 🎓 Introduction to Multi-tenant architecture

Welcome to the Symfony and muti-tenant architecture!  
This project is based on the tutorial Implement [Multi Tenant Architecture in Symfony](https://dev.to/tbeaumont79/implement-multi-tenant-architecture-in-symfony-4l1l).

---

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
- MySQL 8

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

A tenantt is a groups of users (e.g a company, departmentn or client) that shares common access to the software.
Each tenant's data is isolated and secured from others

Isolation levels :
- Database per tenant: Each tenant gets a separate database. Strong isolation, but harder to scale with thousands of tenants.
- Schema per tenant: One database, multiple schemas (one per tenant). Balance between isolation and scalability.
- Shared schema: A single database and schema, but rows are tagged with a tenant_id. Most resource-efficient, 
but requires strong access control.

