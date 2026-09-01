# LMS — Система управления обучением

Система управления обучением для образовательных учреждений.

**Стек:** Laravel 10, Livewire 3, Jetstream, Tailwind CSS, MySQL 8.0, Docker

---

## Быстрый старт (Docker)

### Требования

- [Docker Desktop](https://www.docker.com/products/docker-desktop/) (Windows/Mac) или Docker Engine (Linux)
- [Git](https://git-scm.com/)
- ~2 ГБ свободного места

### 1. Клонирование и запуск

```bash
git clone https://github.com/52zov52/lms.git
cd lms
cp .env.example .env
docker compose up -d
```

> Первый запуск занимает 3-5 минут — собирается образ PHP, устанавливаются зависимости, выполняются миграции и сидеры.

### 2. Проверка

| Сервис | URL |
|---|---|
| Сайт | http://localhost:8000 |
| phpMyAdmin | http://localhost:8080 |

Данные для phpMyAdmin: `root` / `secret`

### 3. Настройка портов

Порты настраиваются в `.env`:

| Переменная | По умолчанию | Описание |
|---|---|---|
| `BACKEND_HTTP_PORT` | `8000` | Порт сайта |
| `PHPMYADMIN_PORT` | `8080` | Порт phpMyAdmin |
| `APP_UID` / `APP_GID` | `1000` | UID/GID пользователя (Linux: `id -u` / `id -g`) |

---

## Команды

### Через just (рекомендуется)

```bash
just shell          # Войти в контейнер php-fpm
just db             # Сброс БД + сидеры
just lint           # PHP CS Fixer (Pint)
just test           # Запуск тестов
just docs           # Генерация API-документации
```

### Через docker compose

```bash
docker compose exec --user www php-fpm bash
docker compose exec --user www php-fpm php artisan migrate:fresh --seed
docker compose exec --user www php-fpm php artisan test
docker compose exec --user www php-fpm php artisan tinker
```

### Фронтенд

```bash
npm install
npm run dev         # Режим разработки
npm run build       # Продакшен (собирает в public/build/)
```

---

## Остановка

```bash
docker compose down          # Остановить (данные БД сохраняются)
docker compose down -v       # Остановить + удалить данные БД
```

---

## Архитектура

- **Роли:** Студент, Преподаватель, Сотрудник (через Spatie Permission)
- **Модули:** Оценки, Посещаемость, Расписание, Документы, Воинский учёт, Социальный паспорт, Асинхронный чат, Электронный журнал и др.
- **Отчёты:** PDF/Excel генерация ведомостей, приказов, отчётов
- **QR-коды:** Верификация документов
