# DEVELOPMENT.md

## Prerequisites

- Docker 24+
- Docker Compose 2+

## Quick Start

```bash
# Клонирование
git clone <repo-url>
cd ai-for-developers-project-386

# Запуск
docker compose up -d

# Установка зависимостей и миграции (первый запуск)
docker compose exec backend composer install
docker compose exec backend php artisan migrate
docker compose exec frontend npm install
```

## Development Workflow

### Бэкенд (Laravel)

```bash
# Запуск тестов
docker compose exec backend php artisan test

# Запуск конкретного теста
docker compose exec backend php artisan test --filter=EventTypeTest

# Линтинг
docker compose exec backend composer lint

# Миграции
docker compose exec backend php artisan migrate
docker compose exec backend php artisan migrate:rollback
docker compose exec backend php artisan migrate:fresh

# Tinker (REPL)
docker compose exec backend php artisan tinker
```

### Фронтенд (VueJS 3)

```bash
# Dev-сервер (hot reload, порт 5173)
docker compose exec frontend npm run dev

# Продакшн-сборка
docker compose exec frontend npm run build

# Линтинг
docker compose exec frontend npm run lint
```

## Testing

### Правила написания тестов

- **PHPUnit** для бэкенда
- Паттерн **AAA** (Arrange → Act → Assert)
- Тесты на контроллеры — интеграционные (Feature)
- Тесты на сервисы — юнит-тесты (Unit)
- Каждый тест-кейс проверяет один сценарий

### Пример структуры теста

```php
public function test_guest_can_create_booking(): void
{
    // Arrange
    $eventType = EventType::factory()->create();
    $data = [...];

    // Act
    $response = $this->postJson('/api/bookings', $data);

    // Assert
    $response->assertStatus(201);
}
```

### Что тестируем

- Создание типа события
- Получение списка типов событий
- Получение свободных слотов
- Создание бронирования
- Запрет на пересекающиеся бронирования
- Валидация входных данных (Form Requests)

## Code Style

### PHP (PSR-12)

- 4 пробела для отступов
- Строки до 120 символов
- `declare(strict_types=1)` в каждом файле
- Именование методов — camelCase
- Именование классов — PascalCase

### JavaScript / Vue

- 2 пробела для отступов
- `<script setup>` во всех компонентах
- Именование компонентов — PascalCase
- Именование composables — camelCase с префиксом `use`
- UI-компоненты — Vuetify (Material Design)
- Стили компонентов — scoped `<style>`, Vuetify-классы

## Contract First

Перед написанием кода:

1. Создать TypeSpec-спецификацию в `specs/`
2. Сгенерировать OpenAPI-конфигурацию
3. Написать код на основе спецификации

## Prism Mock API

Для фронтенд-разработки без реального бэкенда.

```bash
# Скопировать сгенерированный OpenAPI-спек в specs/ (нужно для Prism)
cp tsp-output/schema/openapi.yaml specs/openapi.yaml

# Запуск mock-сервера (порт 4010)
docker compose --profile mock up -d prism

# Остановка
docker compose stop prism
```

Prism читает `specs/openapi.yaml`, валидирует запросы/ответы по схеме и генерирует мок-данные.

### Конфигурация Vite для проксирования

Vite проксирует `/api` на Prism в Docker:

```js
// vite.config.js
export default defineConfig({
  server: {
    proxy: {
      '/api': 'http://localhost:4010'
    }
  }
})
```

## Directory Conventions

```
src/
├── backend/                       # Корень Laravel-проекта
│   ├── app/
│   │   ├── Http/
│   │   │   ├── Controllers/       # Тонкие контроллеры
│   │   │   └── Requests/          # Form Requests
│   │   ├── Models/                # Eloquent-модели
│   │   ├── Services/              # Сервисы с бизнес-логикой
│   │   ├── Domain/                # Entities, Value Objects, Enums
│   │   └── Repositories/          # Репозитории
│   ├── database/
│   │   └── migrations/
│   ├── routes/
│   │   └── api.php
│   └── tests/
│       ├── Feature/               # Интеграционные тесты
│       └── Unit/                  # Юнит-тесты
└── frontend/                      # Корень Vite-проекта
    ├── src/
    │   ├── api/                   # API-клиент
    │   ├── components/            # UI-компоненты
    │   ├── composables/           # Vue composables
    │   ├── router/                # Vue Router
    │   ├── stores/                # Pinia
    │   └── views/                 # Компоненты страниц
    ├── public/
    ├── index.html
    ├── package.json
    └── vite.config.js
```

## API Guidelines

- RESTful, имена ресурсов — множественное число
- Ответы в формате JSON
- Поля в БД — snake_case
- Поля в JSON — camelCase
- Стандартные HTTP-коды: 200, 201, 422, 404
- Без пагинации (малые объёмы данных)

## Database

- SQLite3
- Файл БД: `src/backend/database/database.sqlite`
- Миграции — стандартные Laravel Migrations
- Factories и Seeders для тестовых данных
- Нет внешних ключей (ограничение SQLite в Laravel)
