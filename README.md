# Анализатор страниц (PHP)

[![Quality gate status](https://sonarcloud.io/api/project_badges/measure?project=MaksizPro_middle-php-project-9&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=MaksizPro_middle-php-project-9)
[![hexlet-check](https://github.com/MaksizPro/middle-php-project-9/actions/workflows/hexlet-check.yml/badge.svg)](https://github.com/MaksizPro/middle-php-project-9/actions)

Создадите полноценное веб-приложение, которое выполняет запросы по сети и сохраняет данные в базу данных. Настроите CI и выполните деплой.

Учебный проект Хекслета: https://ru.hexlet.io/programs/middle-php
Как это должно работать: https://files.hexlet.app/a/tefi04

## Ссылка

Ссылка для проверки: https://middle-php-project-9.onrender.com/

## Стек

- PHP

## Установка

<!-- Опишите установку: клонирование, зависимости, переменные окружения -->

```bash
git clone https://github.com/MaksizPro/middle-php-project-9.git
cd middle-php-project-9
```

После клонирования запустить:

```bash
make setup
```

Переменные окружения
---
- DATABASE_URL - подключение к БД (pgsql://user:password@host:port/dbname)
- DISPLAY_ERROR_DETAILS - детализировать вывод ошибок (по умолчанию false)

---
Файл .env
---
В корне можно создать файл **.env** и записывать в него переменные окружения.

## Использование

<!-- Добавьте примеры запуска и запись asciinema — именно это смотрит работодатель -->

---

<details>
<summary>Автоматические тесты Хекслета</summary>

Тесты запускаются на каждый коммит. За запуск отвечает файл `.github/workflows/hexlet-check.yml` — не удаляйте и не переименовывайте ни его, ни репозиторий.

</details>
