#!/usr/bin/env sh
docker compose up -d
docker compose exec app composer i
docker compose exec app bin/console doctrine:database:create -edev
docker compose exec app bin/console doctrine:database:create -etest
docker compose exec app bin/console doctrine:migrations:migrate -edev -n
docker compose exec app bin/console doctrine:migrations:migrate -etest -n
docker compose exec app vendor/bin/phpunit
