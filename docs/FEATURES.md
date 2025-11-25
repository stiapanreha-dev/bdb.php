# Функциональные модули

## Система модулей

Модули включаются/отключаются через `/admin/modules`.

| Модуль | Ключ | Описание |
|--------|------|----------|
| Магазин | `shop` | Интернет-магазин |
| Рассылки | `newsletters` | Рассылки закупок |
| Объявления | `announcements` | Доска объявлений |
| Статьи | `articles` | Статьи пользователей |
| Идеи | `ideas` | Идеи и предложения |

**Использование:**
```php
// В роутах
Route::middleware(['module:shop'])->group(function () { ... });

// В коде
if (module_enabled('newsletters')) { ... }

// В Blade
@if(ModuleSetting::isModuleEnabled('shop'))
```

## Магазин (Shop)

**URL:** /shop

**Функции:**
- Каталог товаров с категориями
- Карточка товара (Editor.js описание)
- Покупка за баланс
- Мои покупки (/shop/my-purchases)
- Скачивание прикреплённых файлов

**Интеграция с балансом:**
1. Проверка баланса
2. DB транзакция
3. Списание средств
4. Создание записи покупки
5. Запись в transactions

## Платежи ЮKassa

**Конфиг:** `config/services.php` → `yookassa`

**Маршруты:**
```php
POST /payment/create       # Создание платежа
GET  /payment/callback     # Возврат после оплаты
GET  /payment/history      # История платежей
POST /payment/webhook      # Webhook от ЮKassa (публичный)
```

**Тестовые карты:**
- Успешно: `5555 5555 5555 4477`
- Отклонено: `5555 5555 5555 5599`

## Рассылки (Newsletters)

**Artisan:**
```bash
php artisan newsletters:send     # Отправка
php artisan newsletters:renew    # Продление (500₽/мес)
```

**Таблицы:**
- `newsletters` - рассылки пользователей
- `newsletter_keywords` - ключевые слова
- `newsletter_logs` - логи отправок
- `newsletter_settings` - глобальные настройки

## Тикеты поддержки

**Для пользователей:** /tickets
**Для админов:** /admin/tickets

**Статусы:** new, in_progress, closed

## Editor.js

Используется в: News, Announcement, Article, ShopProduct

**Рендеринг:**
```blade
@editorJsRender($content)
```

**Плагины:** Header, List (EditorjsList), SimpleImage, Quote, Delimiter, Table, Checklist, Embed

**Загрузка изображений:**
- `/api/upload-image` - общая
- `/api/upload-shop-image` - для магазина
- `/api/upload-announcement-images` - до 5 изображений

## Админ-панель

| Страница | URL |
|----------|-----|
| Пользователи | /admin/users |
| Идеи | /admin/ideas |
| Тарифы | /admin/tariffs |
| Платежи | /admin/payments |
| Рассылки | /admin/newsletters |
| Настройки рассылки | /admin/newsletter-settings |
| SQL Запросы | /admin/sql |
| Тикеты | /admin/tickets |
| Кеш | /admin/cache |
| Модули | /admin/modules |
| Категории магазина | /admin/shop/categories |
| Товары магазина | /admin/shop/products |
| Статистика продаж | /admin/shop/statistics |
| История покупок | /admin/shop/purchases |

## SQL Admin Panel

**URL:** /admin/sql

**Возможности:**
- SELECT запросы к любой БД
- Подключения: pgsql, mssql, mssql_2020-2026, mssql_cp1251
- Лимит: 1000 строк, таймаут 10 сек

**Безопасность:** Только SELECT, блокируются DROP, DELETE, UPDATE, INSERT и др.
