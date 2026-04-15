.PHONY: install docker-up composer-install composer-update composer-dump-autoload \
        run-tests db-create-dev db-create-test db-drop-dev db-drop-test \
        db-migrate-dev db-migrate-test jwt-generate-keys fixture-load-dev fixture-load-test

EXEC = docker compose exec app

install: docker-up composer-install db-drop-dev db-drop-test db-create-dev db-create-test db-migrate-dev db-migrate-test jwt-generate-keys fixture-load-dev fixture-load-test run-tests

docker-up:
	docker compose up -d

composer-install:
	$(EXEC) composer install

composer-update:
	$(EXEC) composer update

composer-dump-autoload:
	$(EXEC) composer dump-autoload

run-tests:
	$(EXEC) vendor/bin/phpunit

db-create-dev:
	$(EXEC) bin/console doctrine:database:create --if-not-exists --env=dev

db-create-test:
	$(EXEC) bin/console doctrine:database:create --if-not-exists --env=test

db-drop-dev:
	$(EXEC) bin/console doctrine:database:drop --force --if-exists --env=dev

db-drop-test:
	$(EXEC) bin/console doctrine:database:drop --force --if-exists --env=test

db-migrate-dev:
	$(EXEC) bin/console doctrine:migrations:migrate --no-interaction --env=dev

db-migrate-test:
	$(EXEC) bin/console doctrine:migrations:migrate --no-interaction --env=test

jwt-generate-keys:
	$(EXEC) bin/console lexik:jwt:generate-keypair --overwrite --no-interaction

fixture-load-dev:
	$(EXEC) bin/console doctrine:fixtures:load --no-interaction --env=dev

fixture-load-test:
	$(EXEC) bin/console doctrine:fixtures:load --no-interaction --env=test
