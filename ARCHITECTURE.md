# ARCHITECTURE.md

## Overview

**Call me in time** — SPA-приложение для бронирования слотов в календаре. Состоит из Laravel API (backend) и VueJS 3 SPA (frontend), разворачивается через Docker Compose.

## System Diagram

```
┌──────────────────────────────────────────────────────────────────┐
│                        Nginx (опционально)                        │
├──────────────────────┬────────────────────┬──────────────────────┤
│   Laravel API :8000  │  VueJS 3 SPA :5173│  Prism Mock :4010    │
│   PHP 8.5            │  Vite Dev Server   │  OpenAPI-валидация   │
│   SQLite3            │  Vuetify UI        │  Мок-ответы          │
└──────────────────────┴────────────────────┴──────────────────────┘
```

## Backend (Laravel API)

### Слоевая архитектура

```
HTTP Request
  └─► Route (api.php)
       └─► Controller (тонкий, только вызов сервиса)
            └─► Form Request (валидация)
            └─► Service (бизнес-логика)
                 ├─► Domain (Entities, Value Objects, Enums)
                 └─► Repository (доступ к данным)
                      └─► Eloquent Model
                           └─► SQLite3
```

### Слои

| Слой | Директория | Ответственность |
|---|---|---|
| Controllers | `app/Http/Controllers/` | Приём запроса, вызов сервиса, возврат JSON-ответа. Тонкие. |
| Form Requests | `app/Http/Requests/` | Валидация входящих данных |
| Services | `app/Services/` | Бизнес-логика приложения |
| Domain | `app/Domain/` | Entities, Value Objects, Enums — доменные понятия |
| Repositories | `app/Repositories/` | Обёртка над Eloquent для доступа к данным |
| Models | `app/Models/` | Eloquent-модели (маппинг на таблицы БД) |

### Сущности и API-эндпоинты

#### EventType (Тип события)

```
POST   /api/event-types           — создать тип события
GET    /api/event-types           — список типов событий
GET    /api/event-types/{id}      — детали типа события
```

#### Booking (Бронирование)

```
POST   /api/bookings              — создать бронирование
GET    /api/bookings              — список бронирований (владелец)
GET    /api/bookings/{id}         — детали бронирования
```

#### Slots (Свободные слоты)

```
GET    /api/event-types/{id}/slots — доступные слоты на 14 дней
```

### База данных

#### Таблица `event_types`

| Поле | Тип | Описание |
|---|---|---|
| id | integer (PK) | Идентификатор |
| name | string | Название |
| description | text | Описание |
| duration | integer | Длительность в минутах |
| created_at | datetime | |
| updated_at | datetime | |

#### Таблица `bookings`

| Поле | Тип | Описание |
|---|---|---|
| id | integer (PK) | Идентификатор |
| event_type_id | integer | ID типа события |
| guest_name | string | Имя гостя |
| guest_email | string | Email гостя |
| start_time | datetime | Начало встречи |
| end_time | datetime | Конец встречи (start_time + duration) |
| created_at | datetime | |
| updated_at | datetime | |

### Правило занятости

Два бронирования не могут пересекаться по времени, независимо от типа события. Проверка на уровне сервиса (не БД, так как SQLite в Laravel без внешних ключей и constraint'ов).

## Frontend (VueJS 3 SPA)

### Технологический стек

- **VueJS 3** — SPA-фреймворк
- **Vuetify** — Material Design компоненты (кнопки, формы, датапикеры, диалоги)
- **Vite** — сборщик и dev-сервер с HMR
- **Pinia** — управление состоянием
- **Vue Router** — клиентская маршрутизация
- **Prism** — mock-сервер API для разработки без бэкенда

### Структура

```
src/
├── components/        # Переиспользуемые UI-компоненты
│   ├── AppHeader.vue
│   ├── Calendar.vue
│   └── BookingForm.vue
├── composables/       # Переиспользуемая логика (useBooking, useSlots)
├── router/            # Vue Router
│   └── index.js
├── stores/            # Pinia stores (booking, eventType)
├── views/             # Компоненты-страницы
│   ├── AdminView.vue          # Админ-панель (владелец календаря)
│   ├── EventTypesView.vue     # Список типов событий (гость)
│   ├── CalendarView.vue       # Выбор слота (гость)
│   └── BookingsView.vue       # Список бронирований (владелец)
├── App.vue
└── main.js
```

### Маршруты

| Путь | Компонент | Роль | Описание |
|---|---|---|---|
| `/` | EventTypesView | Гость | Список типов событий |
| `/event-types/:id` | CalendarView | Гость | Календарь со слотами |
| `/admin` | AdminView | Владелец | Админ-панель |
| `/admin/event-types` | EventTypesManageView | Владелец | Управление типами событий |
| `/admin/bookings` | BookingsView | Владелец | Список бронирований |

### State Management (Pinia)

- **eventTypeStore** — список типов событий
- **bookingStore** — бронирования, свободные слоты

### Поток данных

1. Приложение загружается → запрос `GET /api/event-types` → список типов событий
2. Гость выбирает тип → `GET /api/event-types/{id}/slots` → список свободных слотов на 14 дней
3. Гость выбирает слот → `POST /api/bookings` → создание бронирования
4. Владелец → `GET /api/bookings` → список всех бронирований

## Mock API (Prism)

Prism запускается как Docker-сервис и читает OpenAPI-спеку из `specs/`. Фронтенд в режиме разработки направляет запросы к Prism вместо реального бэкенда.

```
┌──────────────────────────────────────────────────────────┐
│  Prism Mock Server :4010                                  │
│  ← specs/openapi.yaml                                     │
│                                                           │
│  POST /api/bookings → 201 (валидация по схеме)            │
│  GET  /api/event-types → 200 (пример из examples)         │
│  GET  /api/event-types/{id}/slots → 200                   │
└──────────────────────────────────────────────────────────┘
```

Prism валидирует запросы/ответы по OpenAPI-схеме и генерирует мок-данные на основе примеров из спецификации.

## Docker

```
docker/
├── backend/
│   └── Dockerfile
├── frontend/
│   └── Dockerfile
docker-compose.yml
```

- `backend` — PHP 8.5, Composer, Laravel, порт 8000
- `frontend` — Node.js, Vite dev server, порт 5173
- `prism` — Stoplight Prism, mock API server, порт 4010
