.PHONY: up down build restart ps logs app composer validate autoload test test-unit test-integration test-feature test-e2e

up:
	docker compose up -d

down:
	docker compose down

build:
	docker compose up -d --build

restart:
	docker compose down
	docker compose up -d

ps:
	docker compose ps

logs:
	docker compose logs -f

app:
	docker compose exec app sh

composer:
	docker compose exec app composer install

validate:
	docker compose exec app composer validate

autoload:
	docker compose exec app composer dump-autoload

test:
	docker compose exec app composer test

test-unit:
	docker compose exec app composer test:unit

test-integration:
	docker compose exec app composer test:integration

test-feature:
	docker compose exec app composer test:feature

test-e2e:
	docker compose exec app composer test:e2e

analyse:
	docker compose exec app composer analyse

cs:
	docker compose exec app composer cs

cs-fix:
	docker compose exec app composer cs:fix