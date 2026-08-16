# AGENTS.md

## Project

**Call me in time** — упрощенный аналог Cal.com. Календарь звонков с выбором даты и времени.

## Stack

- **Backend:** PHP 8.5, Laravel (latest), SQLite3
- **Frontend:** VueJS 3, Vuetify (Material Design компоненты), Vite (сборщик), SPA
- **Mock API:** Prism (mock-сервер на основе OpenAPI-спек)
- **Infra:** Docker, Docker Compose
- **MCP Servers:** Vuetify MCP Server (документация компонентов Vuetify в IDE)

## Development Approach

**Contract First:**
1. TypeSpec-спецификация
2. OpenAPI-конфигурация
3. Код

**Testing:**
- PHPUnit для контроллеров и сервисов
- Паттерн AAA (Arrange → Act → Assert)

## Project Structure

```
/
├── docker/                  # Docker-конфигурация
├── src/
│   ├── backend/             # Laravel API
│   │   ├── app/
│   │   │   ├── Http/
│   │   │   │   ├── Controllers/   # Тонкие контроллеры
│   │   │   │   └── Requests/      # Form Requests (валидация)
│   │   │   ├── Models/            # Eloquent-модели
│   │   │   ├── Services/          # Бизнес-логика
│   │   │   ├── Domain/            # Доменный слой (Entities, Value Objects, Enums)
│   │   │   └── Repositories/      # Репозитории
│   │   ├── database/
│   │   │   └── migrations/
│   │   ├── routes/
│   │   │   └── api.php            # API-роуты
│   │   └── tests/
│   │       ├── Feature/           # Интеграционные тесты контроллеров
│   │       └── Unit/              # Юнит-тесты сервисов
│   └── frontend/            # VueJS 3 SPA (корень Vite-проекта)
│       ├── src/
│       │   ├── api/               # API-клиент (fetch-обёртка)
│       │   ├── components/        # Однофайловые компоненты (.vue)
│       │   ├── composables/       # Переиспользуемая логика
│       │   ├── router/            # Vue Router
│       │   ├── stores/            # Pinia stores
│       │   └── views/             # View-компоненты для страниц
│       ├── public/
│       ├── index.html
│       ├── package.json
│       └── vite.config.js
├── specs/                   # TypeSpec и OpenAPI-спецификации
├── docs/                    # Документация
├── AGENTS.md
├── ARCHITECTURE.md          # Принятые архитектурные подходы
├── DEVELOPMENT.md           # Описание для локальной разработки проекта
└── README.md                # Описание проекта               
```

## Conventions

### PHP / Laravel
- PSR-12
- Тонкие контроллеры: только вызов сервиса и возврат ответа
- Бизнес-логика — только в Services
- Доменный слой: Entities, Value Objects, Enums
- Репозитории — обёртка над Eloquent моделями
- Валидация — Form Requests
- Без кэширования

### VueJS 3
- Однофайловые компоненты с `<script setup>`
- UI-компоненты — Vuetify (Material Design)
- Сборка — Vite
- State management — Pinia
- Роутинг — Vue Router
- Именование компонентов — PascalCase
- Именование composables — camelCase с префиксом `use`

### Prism (Mock API)
- Mock-сервер на основе OpenAPI-спек из `specs/`
- Запускается в Docker Compose
- Используется для фронтенд-разработки без реального бэкенда

### API Design
- RESTful
- JSON-ответы
- Имена ресурсов во множественном числе (snake_case в БД, camelCase в JSON)
- Коды ответов по стандарту HTTP

### Database
- SQLite3
- Миграции через Laravel Migrations
- Без внешних ключей (ограничение SQLite в Laravel-миграциях)

## Key Commands

```bash
# Запуск проекта
docker compose up -d

# Бэкенд
docker compose exec backend php artisan migrate
docker compose exec backend php artisan test
docker compose exec backend composer lint

# Фронтенд
docker compose exec frontend npm run dev
docker compose exec frontend npm run build
docker compose exec frontend npm run lint
```

## Roles

Две условные роли без регистрации и авторизации:
- **Владелец календаря** — один предзаданный профиль (админ-часть)
- **Гость** — бронирует слоты без аккаунта
