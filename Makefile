composer-bash:
	docker run --rm -it -v `pwd`/:/app composer:2 bash

composer-install:
	docker run --rm -v `pwd`/:/app composer:2 composer install

run-all-tests:
	make run-php82-tests
	make run-php83-tests
	make run-php84-tests

run-php82-tests:
	docker run --rm -v `pwd`/:/app php:8.2 /app/vendor/phpunit/phpunit/phpunit --testdox --bootstrap /app/vendor/autoload.php /app/test/phpunit

run-php83-tests:
	docker run --rm -v `pwd`/:/app php:8.3 /app/vendor/phpunit/phpunit/phpunit --testdox --bootstrap /app/vendor/autoload.php /app/test/phpunit

docs-dev:
	npm run docs:dev

docs-build:
	npm run docs:build

docs-build-deploy:
	DOCS_BASE=/hltv-demo-parser/ npm run docs:build

run-php84-tests:
	docker run --rm -v `pwd`/:/app php:8.4 /app/vendor/phpunit/phpunit/phpunit --testdox --bootstrap /app/vendor/autoload.php /app/test/phpunit
