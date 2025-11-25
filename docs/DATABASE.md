# База данных

## Подключения

### PostgreSQL (основная БД Laravel)

**Подключение:** `pgsql` (по умолчанию)

Содержит все таблицы приложения: users, transactions, payments, news, ideas, announcements, articles, shop_*, newsletters и др.

### MS SQL Server (внешняя БД)

| Подключение | База | Описание |
|-------------|------|----------|
| `mssql` | business2025 | UTF-8, основная |
| `mssql_cp1251` | business2025 | CP1251, для русского текста |
| `mssql_2020` - `mssql_2026` | business20XX | Закупки по годам |

**ВАЖНО:**
- Таблица `db_companies` находится ТОЛЬКО в `business2025`
- Справочники (db_rubrics, db_subrubrics, db_cities) также в `business2025`
- Закупки (zakupki) распределены по годам

## Примеры использования

```php
// PostgreSQL (по умолчанию)
User::where('email', 'test@example.com')->first();
DB::table('news')->get();

// Компании (всегда business2025, cp1251 для русского)
DB::connection('mssql_cp1251')->table('db_companies')->get();

// Закупки по годам
DB::connection('mssql')->table('zakupki')->get();        // 2025
DB::connection('mssql_2024')->table('zakupki')->get();   // 2024
DB::connection('mssql_2023')->table('zakupki')->get();   // 2023
```

## Таблицы MS SQL Server

**business2025 (основная):**
- `db_companies` - компании
- `db_rubrics` - рубрики
- `db_subrubrics` - подрубрики
- `db_cities` - города
- `zakupki` - закупки 2025
- `zakupki_specification` - спецификации

## Таблицы PostgreSQL

| Таблица | Описание |
|---------|----------|
| `users` | Пользователи (balance, role, avatar) |
| `transactions` | Транзакции |
| `payments` | Платежи ЮKassa |
| `tariffs` | Тарифные планы |
| `user_subscriptions` | Подписки |
| `news` | Новости |
| `ideas` | Идеи |
| `announcements` | Объявления |
| `articles` | Статьи |
| `newsletters` | Рассылки |
| `newsletter_keywords` | Ключевые слова |
| `newsletter_logs` | Логи рассылок |
| `tickets` | Тикеты |
| `ticket_messages` | Сообщения тикетов |
| `shop_categories` | Категории товаров |
| `shop_products` | Товары |
| `shop_product_views` | Просмотры |
| `shop_product_purchases` | Покупки |
| `module_settings` | Модули |

## .env конфигурация

**Локально:**
```env
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_DATABASE=businessdb
DB_USERNAME=postgres

MSSQL_HOST=172.26.192.1
MSSQL_DATABASE=buss
```

**Продакшн:**
```env
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_DATABASE=businessdb
DB_USERNAME=businessdb_user

MSSQL_HOST=172.26.192.1
MSSQL_DATABASE=business2025
```
