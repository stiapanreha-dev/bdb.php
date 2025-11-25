# Архитектура проекта

## Стек технологий

- **Framework:** Laravel 12.x
- **PHP:** 8.3
- **Frontend:** Blade, Tailwind CSS 3.1, Alpine.js 3.4
- **Сборка:** Vite 7.x
- **Редактор:** Editor.js (alaminfirdows/laravel-editorjs)
- **Экспорт:** Maatwebsite Excel 3.1
- **Платежи:** YooKassa SDK

## Контроллеры

### Основные (app/Http/Controllers/)

| Контроллер | Описание |
|------------|----------|
| `ZakupkiController` | Закупки (главная), year-based БД, маскирование |
| `CompanyController` | Компании (business2025), требует auth |
| `ShopController` | Магазин: каталог, категории, карточка, покупки, скачивание |
| `NewsController` | Новости (Editor.js) |
| `AnnouncementController` | Объявления (Editor.js, до 5 изображений) |
| `ArticleController` | Статьи (модуль, Editor.js) |
| `IdeasController` | Идеи пользователей |
| `NewsletterController` | Рассылки (модуль) |
| `TicketController` | Тикеты для пользователей |
| `PaymentController` | Платежи ЮKassa |
| `SubscriptionController` | Подписки на тарифы |
| `ProfileController` | Профиль (аватар, контакты) |
| `ImageUploadController` | Загрузка изображений для Editor.js |

### Админ (app/Http/Controllers/Admin/)

| Контроллер | Описание |
|------------|----------|
| `AdminController` | Пользователи, идеи, SQL, рассылки, платежи, кеш, модули |
| `TariffController` | CRUD тарифов |
| `ShopCategoryController` | CRUD категорий магазина |
| `ShopProductController` | CRUD товаров, статистика |
| `AdminTicketController` | Управление тикетами |

## Модели (app/Models/)

**Пользователи и финансы:**
- `User` - пользователь (balance, role, avatar)
- `Transaction` - транзакции
- `Payment` - платежи ЮKassa
- `Tariff`, `UserSubscription`, `TariffHistory` - подписки

**Контент:**
- `News` - новости
- `Idea` - идеи
- `Announcement` - объявления
- `Article` - статьи

**Магазин:**
- `ShopCategory` - категории (иерархические)
- `ShopProduct` - товары (с attachment)
- `ShopProductView` - просмотры
- `ShopProductPurchase` - покупки

**Рассылки:**
- `Newsletter` - рассылки
- `NewsletterKeyword` - ключевые слова
- `NewsletterLog` - логи отправок
- `NewsletterSetting` - настройки

**Поддержка:**
- `Ticket` - тикеты
- `TicketMessage` - сообщения

**Система:**
- `ModuleSetting` - управление модулями

## Middleware

| Middleware | Использование |
|------------|---------------|
| `CheckModuleEnabled` | `Route::middleware(['module:shop'])` |
| `EnsureEmailVerified` | Проверка email |
| `EnsurePhoneVerified` | Проверка телефона |

## Artisan команды

```bash
php artisan newsletters:send          # Отправка рассылок
php artisan newsletters:renew         # Продление подписок (500₽/мес)
php artisan db:migrate-from-sqlite    # Миграция из SQLite
php artisan db:fix-sequences          # Исправление PostgreSQL sequences
```

## Views структура

```
resources/views/
├── admin/              # Админ-панель
├── announcements/      # Объявления
├── articles/           # Статьи
├── auth/               # Аутентификация
├── companies/          # Компании
├── components/         # Blade компоненты (app-layout.blade.php)
├── emails/             # Email шаблоны
├── ideas/              # Идеи
├── news/               # Новости
├── newsletters/        # Рассылки
├── payments/           # Платежи
├── profile/            # Профиль
├── shop/               # Магазин
├── static/             # Статические страницы
├── subscriptions/      # Подписки
├── tickets/            # Тикеты
└── zakupki/            # Закупки
```

## Layout

**Единственный layout:** `resources/views/components/app-layout.blade.php`

Использование: `<x-app-layout>...</x-app-layout>`

## Laravel 12 особенности

**Middleware в контроллерах удален:**

```php
// НЕправильно
public function __construct() {
    $this->middleware('auth'); // Ошибка!
}

// Правильно - в routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/companies', [CompanyController::class, 'index']);
});
```
