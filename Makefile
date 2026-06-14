.PHONY: up down build restart ps logs app composer validate autoload

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