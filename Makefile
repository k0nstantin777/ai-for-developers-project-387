.PHONY: up down restart install migrate migrate-fresh test test-filter lint lint-backend lint-frontend dev build e2e prism tinker shell logs

up:
	docker compose up -d

down:
	docker compose down

restart: down up

install:
	docker compose exec backend composer install
	docker compose exec backend php artisan migrate
	docker compose exec frontend npm install

migrate:
	docker compose exec backend php artisan migrate

migrate-fresh:
	docker compose exec backend php artisan migrate:fresh

test:
	docker compose exec backend php artisan test

test-filter:
	docker compose exec backend php artisan test --filter=$(FILTER)

lint: lint-backend lint-frontend

lint-backend:
	docker compose exec backend composer lint

lint-frontend:
	docker compose exec frontend npm run lint

dev:
	docker compose exec frontend npm run dev

build:
	docker compose exec frontend npm run build

e2e:
	docker compose --profile e2e up --abort-on-container-exit e2e

prism:
	docker compose --profile mock up -d prism

tinker:
	docker compose exec backend php artisan tinker

shell:
	docker compose exec backend bash

logs:
	docker compose logs -f
