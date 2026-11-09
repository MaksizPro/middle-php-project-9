PORT ?= 8000

setup: install

install:
	composer install

build: install

start:
	PHP_CLI_SERVER_WORKERS=5 php -S 0.0.0.0:$(PORT) -t public