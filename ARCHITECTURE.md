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

### Структура (src/frontend/ — корень Vite-проекта)

```
src/frontend/
├── src/
│   ├── api/
│   │   └── client.js            # Базовый fetch-клиент
│   ├── components/              # Переиспользуемые UI-компоненты
│   │   ├── AppHeader.vue        # Хедер: название, дата, режим
│   │   ├── AppFooter.vue        # Футер: название, копирайт
│   │   ├── SuccessDialog.vue    # Диалог успеха
│   │   └── ErrorDialog.vue      # Диалог ошибки
│   ├── composables/             # API-логика
│   │   ├── useEventTypes.js     # GET/POST /api/event-types
│   │   ├── useBookings.js       # GET/POST /api/bookings
│   │   └── useSlots.js          # GET /api/event-types/{id}/slots
│   ├── router/
│   │   └── index.js
│   ├── stores/
│   │   └── app.js               # Режим: admin / guest
│   ├── views/
│   │   ├── HomeView.vue           # Выбор роли
│   │   ├── admin/
│   │   │   ├── AdminDashboardView.vue    # Панель администратора
│   │   │   ├── CreateEventTypeView.vue   # Создание типа события
│   │   │   └── AdminBookingsView.vue     # Список всех бронирований
│   │   └── guest/
│   │       ├── GuestEventTypesView.vue   # Список типов событий
│   │       └── GuestBookingView.vue      # Выбор слота + бронирование
│   ├── App.vue
│   └── main.js
├── public/
├── index.html
├── package.json
└── vite.config.js
```

### Маршруты

| Путь | Компонент | Роль | Описание |
|---|---|---|---|
| `/` | HomeView | — | Выбор роли (Admin / Guest) |
| `/admin` | AdminDashboardView | Владелец | Панель администратора |
| `/admin/event-types/create` | CreateEventTypeView | Владелец | Создание типа события |
| `/admin/bookings` | AdminBookingsView | Владелец | Список бронирований |
| `/guest/event-types` | GuestEventTypesView | Гость | Список типов событий |
| `/guest/event-types/:id/booking` | GuestBookingView | Гость | Выбор слота + бронирование |

### State Management (Pinia)

- **appStore** — режим приложения (`admin` / `guest`)

API-состояние (загрузка, ошибки, данные) управляется локально в composables.

### Поток данных

1. Главная страница → выбор роли → сохраняется в appStore
2. Гость: `GET /api/event-types` → список типов → выбор → `GET /api/event-types/{id}/slots` → слоты на 14 дней → `POST /api/bookings`
3. Админ: `GET /api/event-types` + `GET /api/bookings` → просмотр → `POST /api/event-types`

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
