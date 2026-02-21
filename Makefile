.PHONY: up down migrate php-rebuild build test jwt

up:
	docker-compose up -d 

down:
	docker-compose down 

migrate:
	docker-compose exec php php artisan migrate

php-rebuild:
	docker-compose up -d --no-deps --build php

build:
	docker-compose build

test: 
	docker-compose exec php php artisan test

jwt:
	docker-compose exec php php artisan jwt:secret