PORT ?= 8000

setup: install build-css

build: install build-css

install:
	composer install
	npm install

build-css:
	npm run build:css

test-php:
	vendor/bin/phpcs

test-php-clean:
	vendor/bin/phpcbf

start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public -d variables_order=EGPCS