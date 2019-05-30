build:
	docker-compose -f docker-compose.yml build
up:
	docker-compose -f docker-compose.yml up -d

down:
	docker-compose -f docker-compose.yml down

ps:
	docker-compose -f docker-compose.yml ps

stop:
	docker-compose -f docker-compose.yml stop

start:
	docker-compose -f docker-compose.yml start

restart:
	docker-compose -f docker-compose.yml restart

php:
	docker-compose -f docker-compose.yml exec php-fpm bash

nginx:
	docker-compose -f docker-compose.yml exec webserver sh

db:
	docker-compose -f docker-compose.yml exec postgres sh

logs-php:
	docker-compose -f docker-compose.yml logs -f php-fpm
