# Thailand Together - Docker Management
# Usage: make [target] CLUSTER=pattaya

CLUSTER ?= global
COMPOSE = CLUSTER_CODE=$(CLUSTER) docker compose

.PHONY: up down build logs shell migrate seed fresh test

up:
	$(COMPOSE) up -d

down:
	$(COMPOSE) down

build:
	$(COMPOSE) build --no-cache

logs:
	$(COMPOSE) logs -f app

shell:
	docker exec -it tt-app-$(CLUSTER) sh

migrate:
	docker exec tt-app-$(CLUSTER) php artisan migrate

seed:
	docker exec tt-app-$(CLUSTER) php artisan db:seed

fresh:
	docker exec tt-app-$(CLUSTER) php artisan migrate:fresh --seed

test:
	docker exec tt-app-$(CLUSTER) php artisan test

cache-clear:
	docker exec tt-app-$(CLUSTER) php artisan cache:clear
	docker exec tt-app-$(CLUSTER) php artisan config:clear
	docker exec tt-app-$(CLUSTER) php artisan route:clear

status:
	docker ps --filter "name=tt-" --format "table {{.Names}}\t{{.Status}}\t{{.Ports}}"

dev:
	CLUSTER_CODE=$(CLUSTER) docker compose --profile dev up -d
