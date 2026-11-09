PORT ?= 8000

setup: install build-css

build: install build-css

install:
	composer install

build-css:
	npm run build:css

test-php:
	vendor/bin/phpcs ./app
	vendor/bin/phpcs ./public

start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public