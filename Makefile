composer-install:
	docker run --rm -v `pwd`/:/app composer:2 composer install

run-all-tests:
	make run-php73-tests
	make run-php74-tests
	make run-php80-tests
	make run-php81-tests

run-php73-tests:
	docker run --rm -v `pwd`/:/app php:7.3 /app/vendor/phpunit/phpunit/phpunit --testdox --bootstrap /app/vendor/autoload.php /app/tests

run-php74-tests:
	docker run --rm -v `pwd`/:/app php:7.4 /app/vendor/phpunit/phpunit/phpunit --testdox --bootstrap /app/vendor/autoload.php /app/tests

run-php80-tests:
	docker run --rm -v `pwd`/:/app php:8.0 /app/vendor/phpunit/phpunit/phpunit --testdox --bootstrap /app/vendor/autoload.php /app/tests

run-php81-tests:
	docker run --rm -v `pwd`/:/app php:8.1 /app/vendor/phpunit/phpunit/phpunit --testdox --bootstrap /app/vendor/autoload.php /app/tests
