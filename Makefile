up: docker-up
down: docker-down
restart: docker-down docker-up
init: docker-down-clear videohub-clear docker-pull docker-build docker-up videohub-init
test: videohub-test

docker-up:
	docker-compose up -d

docker-down:
	docker-compose down --remove-orphans

docker-down-clear:
	docker-compose down -v --remove-orphans

docker-pull:
	docker-compose pull

docker-build:
	docker-compose build

videohub-init: videohub-composer-install videohub-assets-install videohub-wait-db videohub-migrations videohub-fixtures videohub-ready

videohub-clear:
	docker run --rm -v ${PWD}/project:/app --workdir=/app alpine rm -f .ready

videohub-composer-install:
	docker-compose run --rm videohub-php-cli composer install

videohub-assets-install:
	docker-compose run --rm videohub-node yarn install
	docker-compose run --rm videohub-node npm rebuild node-sass

videohub-wait-db:
	until docker-compose exec -T videohub-postgres pg_isready --timeout=0 --dbname=app ; do sleep 1 ; done

videohub-migrations:
	docker-compose run --rm videohub-php-cli php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration

videohub-fixtures:
	docker-compose run --rm videohub-php-cli php bin/console doctrine:fixtures:load --no-interaction

videohub-ready:
	docker run --rm -v ${PWD}/project:/app --workdir=/app alpine touch .ready

videohub-assets-dev:
	docker-compose run --rm videohub-node npm run dev

watch:
	docker-compose run --rm videohub-node npm run watch

videohub-test:
	docker-compose run --rm videohub-php-cli php bin/phpunit

psql:
	docker exec -it videohub_videohub-postgres_1 psql -U app -W app

fixtures:
	docker-compose run --rm videohub-php-cli php bin/console doctrine:schema:drop -n -q --force --full-database
	docker-compose run --rm videohub-php-cli php bin/console doctrine:migrations:migrate -n -q
	docker-compose run --rm videohub-php-cli php bin/console doctrine:fixtures:load -n -q