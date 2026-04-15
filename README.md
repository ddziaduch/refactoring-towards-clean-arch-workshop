# Refactoring Towards Clean Architecture — Workshop

## Prerequisites

Before you begin, make sure you have the following installed:

- [Git](https://git-scm.com/)
- [Docker Desktop](https://www.docker.com/products/docker-desktop/)
- `make` (macOS: included with Xcode CLT via `xcode-select --install`; Windows: install via [Chocolatey](https://chocolatey.org/) with `choco install make`)

## Setup

### 1. Clone the repository

```bash
git clone <repository-url>
cd refactoring-towards-clean-arch-workshop
```

### 2. Configure environment

Copy the example env file and adjust the values if needed:

```bash
cp .env.local.example .env.local
```

### 3. Install

```bash
make install
```

This single command will: start Docker containers, install dependencies, create and migrate both dev and test databases, load fixtures, generate JWT keys, and run the test suite to verify everything works.

---

## Host installation (without Docker)

Use this only if you cannot run Docker.

**Additional prerequisites:** PHP 8.3, Composer, PostgreSQL 16

1. Clone the repository (see step 1 above)
2. Copy and configure `.env.local`:
   ```bash
   cp .env.local.example .env.local
   ```
   Set `DATABASE_URL` to point to your local PostgreSQL instance:
   ```
   DATABASE_URL="postgresql://user:password@127.0.0.1:5432/app?serverVersion=16&charset=utf8"
   ```
3. Run `composer install`
4. Generate JWT keys: `bin/console lexik:jwt:generate-keypair --overwrite --no-interaction`
5. Create databases:
   - `bin/console doctrine:database:create --if-not-exists --env=dev`
   - `bin/console doctrine:database:create --if-not-exists --env=test`

   > The test database uses a `_test` suffix automatically (e.g. `app` → `app_test`).
6. Run migrations:
   - `bin/console doctrine:migrations:migrate --no-interaction --env=dev`
   - `bin/console doctrine:migrations:migrate --no-interaction --env=test`
7. Load fixtures:
   - `bin/console doctrine:fixtures:load --no-interaction --env=dev`
   - `bin/console doctrine:fixtures:load --no-interaction --env=test`
8. Run `vendor/bin/phpunit` to verify everything works

> For a reinstall, drop the databases first:
> - `bin/console doctrine:database:drop --force --if-exists --env=dev`
> - `bin/console doctrine:database:drop --force --if-exists --env=test`
