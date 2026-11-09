# Образ для деплоя на render. Править его не нужно: сборка зовёт `make build`,
# запуск зовёт `make start`, и всё, что меняется по шагам, живёт в Makefile.
FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends libzip-dev libpq-dev make unzip nodejs npm \
    && docker-php-ext-install zip pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

WORKDIR /app

COPY . .
RUN make build

CMD ["make", "start"]
