.PHONY: up down migrate php-rebuild

up:
	docker-compose up -d 

down:
	docker-compose down 

migrate:
	docker-compose exec php php artisan migrate

php-rebuild:
	docker-compose up -d --no-deps --build php