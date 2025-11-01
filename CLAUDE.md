# BusinessDB - Документация для разработки

## Обзор проекта

Laravel 11 приложение для работы с базой данных компаний и закупок.

**URL продакшена:** https://businessdb.ru/

## Архитектура

### Стек технологий
- **Framework:** Laravel 11.x
- **PHP:** 8.2+ (локально 8.3, продакшн 8.2)
- **База данных:**
  - **PostgreSQL** - основная БД для Laravel (users, news, ideas, transactions)
  - **Microsoft SQL Server** - внешняя БД (2 подключения: `mssql` и `mssql_cp1251` для работы с компаниями и закупками)
- **Frontend:** Blade templates, Tailwind CSS, Alpine.js
- **Экспорт:** Maatwebsite Excel (для экспорта в .xlsx)

### Подключения к БД

В проекте используется несколько подключений:

1. **pgsql** (по умолчанию) - PostgreSQL для локальных таблиц Laravel
   - users, news, ideas, transactions и другие таблицы приложения

2. **mssql** - MS SQL Server подключение (UTF-8) к базе business2025
   - Для работы с внешней БД компаний и текущих закупок
   - База: business2025 (основная, содержит db_companies)

3. **mssql_cp1251** - MS SQL Server с кодировкой CP1251 к базе business2025
   - Для работы с VARCHAR полями на русском языке (компании, рубрики)
   - База: business2025 (основная, содержит db_companies)

4. **mssql_2020 - mssql_2026** - MS SQL Server подключения к базам данных по годам
   - Для работы с закупками (zakupki) за разные годы
   - Базы: business2020, business2021, business2022, business2023, business2024, business2025, business2026

**⚠️ ВАЖНО: Структура БД**
- **Таблица компаний (db_companies)** находится ТОЛЬКО в базе **business2025**
- **Справочники** (db_rubrics, db_subrubrics, db_cities) также в **business2025**
- **Закупки (zakupki)** распределены по базам данных в зависимости от года

Пример использования:
```php
// Локальные таблицы (по умолчанию PostgreSQL)
User::where('email', 'test@example.com')->first();
DB::table('news')->get();

// Для текстовых полей на русском из MS SQL используем cp1251
// Компании ВСЕГДА в business2025
DB::connection('mssql_cp1251')->table('db_companies')...

// Для закупок используем соответствующую базу данных по году
DB::connection('mssql')->table('zakupki')... // текущий год (business2025)
DB::connection('mssql_2024')->table('zakupki')... // закупки 2024 года
DB::connection('mssql_2023')->table('zakupki')... // закупки 2023 года
```

## Окружения

### Локальная разработка (Devilbox)

**Путь:** `/home/lexun/devilbox/data/www/businessdb`
**URL:** https://businessdb.dvl.to/
**Docker контейнеры:**
- devilbox-php83-1 (PHP 8.3)
- devilbox-pgsql-1 (PostgreSQL 15)

**База данных:** PostgreSQL
- Host: pgsql
- Port: 5432
- Database: businessdb
- Username: postgres
- Password: (пустой)

### Продакшн (Ubuntu Server)

**Сервер:** moon (176.117.212.121)
**SSH:** ssh XBMC (настроен в ~/.ssh/config)
**SSH ключ:** ~/.ssh/id_smtb
**OS:** Ubuntu (Linux 6.8.0-86-generic)
**Путь проекта:** /home/alex/businessdb
**Пользователь проекта:** alex
**Группа:** www-data
**Веб-сервер:** Nginx 1.24.0
**PHP:** 8.3.6 (CLI) NTS

#### Конфигурация Nginx

Конфигурация сервера находится в `/etc/nginx/sites-available/businessdb.ru`

**Основные директивы:**
- Домен: businessdb.ru, www.businessdb.ru
- Document Root: /home/alex/businessdb/public
- PHP-FPM: через unix socket или TCP
- SSL сертификаты (Let's Encrypt)

#### Необходимые PHP расширения

Должны быть установлены через `apt`:
```bash
sudo apt install php8.3-{cli,fpm,pgsql,pdo,sqlite3,gd,zip,mbstring,xml,curl,sqlsrv,pdo-sqlsrv}
```

**Драйверы SQL Server для Linux:**
- Установка через Microsoft репозиторий
- Пакеты: php8.3-sqlsrv, php8.3-pdo-sqlsrv

## Процесс деплоя

### Git workflow

1. **Локальные изменения:**
   ```bash
   cd /home/lexun/devilbox/data/www/businessdb
   # Внести изменения
   git add .
   git commit -m "Описание изменений"
   git push origin main
   ```

2. **Деплой на продакшн:**
   ```bash
   ssh XBMC
   cd /home/alex/businessdb
   git pull origin main
   ```

3. **Если нужны composer зависимости:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

4. **Перезапуск сервисов:**
   ```bash
   sudo systemctl restart php8.3-fpm
   sudo systemctl reload nginx
   ```

### Автоматический деплой (GitHub Actions)

**Настроен автоматический деплой при push в main ветку:**

1. Workflow выполняется в GitHub Actions
2. Подключается к серверу по SSH
3. Выполняет `git pull origin main`
4. Выполняет `php artisan optimize:clear` (очищает все кеши включая opcache)
5. Перезапускает PHP-FPM: `sudo systemctl restart php8.3-fpm`

**Файл workflow:** `.github/workflows/deploy.yml`

**Проверка статуса деплоя:**
```bash
gh run list --limit 5
gh run view [run_id]
```

### Важно при деплое

- **ВСЕГДА** используйте git для изменений, НЕ редактируйте файлы напрямую на сервере
- При коммитах используйте шаблон с `🤖 Generated with Claude Code` и `Co-Authored-By: Claude`
- Проверяйте `.env` файл на сервере - он не версионируется
- После изменений в `composer.json` не забудьте выполнить `composer install` на сервере
- **Opcache:** После деплоя автоматически очищается через `php artisan optimize:clear`

## Особенности Laravel 11

### Middleware в контроллерах

⚠️ **Важно:** В Laravel 11 метод `$this->middleware()` в конструкторах контроллеров **удален**.

**НЕправильно (вызовет ошибку):**
```php
public function __construct()
{
    $this->middleware('auth'); // ❌ Ошибка!
}
```

**Правильно (используйте routes):**
```php
// routes/web.php
Route::middleware('auth')->group(function () {
    Route::get('/companies', [CompanyController::class, 'index']);
});
```

## Структура проекта

### Основные контроллеры

- `ZakupkiController` - работа с закупками (главная страница)
  - Поддержка year-based баз данных
  - Оптимизация запросов с CONVERT(DATE) для SQL Server
  - Маскирование данных для неоплаченных пользователей
- `CompanyController` - работа с компаниями (только business2025)
- `AdminController` - админ-панель
  - Управление пользователями (баланс, роли)
  - Модерация идей (одобрение/отклонение/возврат на рассмотрение)
  - **SQL редактор** - выполнение SELECT запросов к базам данных
- `ProfileController` - профиль пользователя (из Laravel Breeze)
- `NewsController` - новости
- `IdeasController` - идеи пользователей

### Helpers

- `DataMaskingHelper` - маскирование email, телефонов, сайтов для пользователей без баланса

### Exports

- `CompaniesExport` - экспорт компаний в Excel
- `ZakupkiExport` - экспорт закупок в Excel (максимум 10 000 записей)

### Статические страницы (resources/views/static/)

- `support.blade.php` - страница технической поддержки
  - Контакты: Telegram (@cdvks), Email (support@businessdb.ru)
  - FAQ с ответами на частые вопросы
  - Полезные ссылки
- `privacy-policy.blade.php` - политика конфиденциальности
- `terms-of-service.blade.php` - пользовательское соглашение
- `offer.blade.php` - публичная оферта
- `contacts.blade.php` - контактная информация

### Views (resources/views/)

**Zakupki (закупки):**
- `zakupki/index.blade.php` - список закупок с фильтрами
- `zakupki/detail.blade.php` - карточка закупки (детальная информация)

**Companies (компании):**
- `companies/index.blade.php` - список компаний с фильтрами
- `companies/show.blade.php` - карточка компании

**Admin (админ-панель):**
- `admin/users.blade.php` - управление пользователями
- `admin/ideas.blade.php` - модерация идей
- `admin/sql.blade.php` - **SQL редактор** для выполнения запросов

**Layouts:**
- `layouts/app.blade.php` - главный layout с навигацией, footer, модалками

### Стили (public/css/)

- `style.css` - глобальные стили приложения
  - CSS переменные: `--primary-color`, `--secondary-color`
  - Стили для карточек, таблиц, форм, кнопок
  - Responsive дизайн
  - Print стили для карточек закупок

### База данных (MS SQL Server)

**⚠️ ВАЖНО: Структура MS SQL Server баз данных**

Данные разделены на несколько баз данных по годам:
- `business2020` - закупки 2020 года
- `business2021` - закупки 2021 года
- `business2022` - закупки 2022 года
- `business2023` - закупки 2023 года
- `business2024` - закупки 2024 года
- `business2025` - **основная база** (компании + закупки 2025 года)
- `business2026` - закупки 2026 года

**Таблицы в базе business2025 (основная):**
- `db_companies` - компании (ТОЛЬКО в business2025!)
- `db_rubrics` - рубрики компаний (ТОЛЬКО в business2025!)
- `db_subrubrics` - подрубрики компаний (ТОЛЬКО в business2025!)
- `db_cities` - города (ТОЛЬКО в business2025!)
- `zakupki` - закупки 2025 года

**Таблицы в базах business2020-2026 (по годам):**
- `zakupki` - закупки соответствующего года
- `zakupki_specification` - спецификации закупок соответствующего года

**Локальные таблицы Laravel (PostgreSQL):**
- `users` - пользователи
- `ideas` - идеи пользователей
- `news` - новости
- `transactions` - транзакции пользователей

## Конфигурация окружения

### .env (локальная разработка)

```env
APP_ENV=local
APP_DEBUG=true
APP_URL=https://businessdb.dvl.to

# PostgreSQL (основная БД)
DB_CONNECTION=pgsql
DB_HOST=pgsql
DB_PORT=5432
DB_DATABASE=businessdb
DB_USERNAME=postgres
DB_PASSWORD=

# MS SQL Server подключения (для внешних данных)
# Локально: buss (единая база для разработки)
# Продакшн: business2025 (основная) + business2020-2026 (по годам)
MSSQL_HOST=172.26.192.1
MSSQL_PORT=1433
MSSQL_DATABASE=buss
MSSQL_USERNAME=sa
MSSQL_PASSWORD=***
```

### .env (продакшн) - TODO: обновить на PostgreSQL

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://businessdb.ru

# TODO: Установить PostgreSQL на Ubuntu Server
# DB_CONNECTION=pgsql
# DB_HOST=localhost
# DB_PORT=5432
# DB_DATABASE=businessdb
# DB_USERNAME=businessdb_user
# DB_PASSWORD=***

# Временно SQLite (устарело)
DB_CONNECTION=sqlite

# MS SQL Server подключения (для внешних данных)
# ⚠️ На продакшене используется business2025 (содержит компании)
# ⚠️ Для работы с закупками по годам используются mssql_2020-2026
MSSQL_HOST=172.26.192.1
MSSQL_PORT=1433
MSSQL_DATABASE=business2025
MSSQL_USERNAME=sa
MSSQL_PASSWORD=***
```

## Troubleshooting

### Проблема: "could not find driver" для MS SQL

**Решение (Ubuntu):**
1. Установите драйверы SQL Server:
   ```bash
   sudo apt install php8.3-sqlsrv php8.3-pdo-sqlsrv
   ```
2. Проверьте что расширения загружены:
   ```bash
   php -m | grep sqlsrv
   ```
3. Перезапустите PHP-FPM:
   ```bash
   sudo systemctl restart php8.3-fpm
   ```

### Проблема: "Call to undefined method middleware()"

**Решение:**
Удалите конструктор `__construct()` с вызовом `$this->middleware()` из контроллера.
Middleware должен быть определен в routes/web.php.

### Проблема: Nginx не запускается

**Диагностика:**
```bash
sudo nginx -t  # Проверка синтаксиса
sudo systemctl status nginx
```

Проверьте логи:
- `/var/log/nginx/error.log`
- `/var/log/nginx/businessdb-error.log`

### Проблема: Ошибка доступа к файлам (403 Forbidden)

**Решение:**
1. Проверьте права на директории:
   ```bash
   sudo chown -R alex:www-data /home/alex/businessdb
   sudo chmod -R 755 /home/alex/businessdb/storage
   sudo chmod -R 755 /home/alex/businessdb/bootstrap/cache
   ```
2. Проверьте конфигурацию Nginx (root, index директивы)

### Проблема: 504 Gateway Timeout / Медленные запросы к SQL Server

**Причина:**
- Нехватка памяти в buffer pool SQL Server
- Отсутствие индексов на полях с фильтрацией (особенно `created` в таблице zakupki)
- Большое количество записей (1.8+ млн в business2025)

**Решение:**

1. **Создать индексы на таблицах zakupki:**
   - Скрипт: `database/sql/create_zakupki_indexes.sql`
   - Документация: `database/sql/README_INDEXES.md`
   - Создает индексы:
     - `idx_zakupki_created` - для фильтрации по датам
     - `idx_zakupki_created_customer` - для комбинированных запросов

2. **Выполнить скрипт на SQL Server:**
   ```bash
   # Через sqlcmd (на Windows Server с SQL Server)
   sqlcmd -S 172.26.192.1 -U sa -P your_password -i database/sql/create_zakupki_indexes.sql

   # Или через SSMS (SQL Server Management Studio)
   # Открыть файл и нажать F5
   ```

3. **Проверить созданные индексы:**
   ```sql
   USE business2025;
   SELECT i.name AS IndexName, c.name AS ColumnName
   FROM sys.indexes i
   INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
   INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
   WHERE i.object_id = OBJECT_ID('zakupki')
   ORDER BY i.name;
   ```

4. **Оптимизация запросов в коде:**
   - Используйте `CONVERT(DATE, created)` вместо `CAST`
   - Всегда указывайте даты в формате 'Y-m-d' (строка)
   - Ограничивайте поиск периодом до 30 дней

**Ожидаемый результат:**
- Ускорение запросов на 50-90%
- Снижение нагрузки на SQL Server
- Устранение ошибок 504 Gateway Timeout

### Проблема: Ошибка "trim(): Argument must be of type string, InputBag given"

**Причина:** Использование `$request->query` (встроенное свойство Laravel) вместо `$request->input('query')`

**Решение:**
```php
// ❌ Неправильно
$query = trim($request->query);

// ✅ Правильно
$query = trim($request->input('query'));
```

## Полезные команды

### SSH подключение к серверу
```bash
ssh XBMC
# или напрямую
ssh -i ~/.ssh/id_smtb alex@176.117.212.121
```

### Проверка PHP модулей на сервере
```bash
php -m
php -v
```

### Проверка git статуса на сервере
```bash
cd /home/alex/businessdb
git status
git log --oneline -5
```

### Очистка кеша Laravel
```bash
php artisan optimize:clear  # Очищает все кеши (config, cache, view, route, opcache)

# Или по отдельности:
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### SQL Admin Panel

**Доступ:** https://businessdb.ru/admin/sql (требуется admin роль)

**Возможности:**
- Выполнение SELECT запросов к любой базе данных
- Выбор подключения: pgsql, mssql (business2025), mssql_2020-2026, mssql_cp1251
- Ограничение результата: максимум 1000 строк
- Таймаут: 10 секунд для MSSQL
- Отображение времени выполнения запроса

**Безопасность:**
- Разрешены только SELECT запросы
- Блокируются операции: DROP, TRUNCATE, DELETE, UPDATE, INSERT, ALTER, CREATE, GRANT, REVOKE, EXEC

**Примеры запросов:**
```sql
-- Последние закупки
SELECT TOP 20 id, created, purchase_object, customer, start_cost
FROM zakupki
ORDER BY id DESC;

-- Компании (только в business2025)
SELECT TOP 20 id, name, city, phone, email
FROM db_companies
ORDER BY id DESC;

-- Проверка индексов
SELECT i.name AS IndexName, c.name AS ColumnName
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id
WHERE i.object_id = OBJECT_ID('zakupki');
```

### Миграция данных из SQLite в PostgreSQL
```bash
# Локально (через Docker)
docker exec devilbox-php83-1 php /shared/httpd/businessdb/artisan db:migrate-from-sqlite

# С указанием пути к SQLite базе
docker exec devilbox-php83-1 php /shared/httpd/businessdb/artisan db:migrate-from-sqlite /path/to/database.sqlite

# На продакшне (после установки PostgreSQL)
php artisan db:migrate-from-sqlite C:\soft\bussiness.db2\bdb.php\database\database.sqlite
```

## Контакты и ресурсы

- **Git репозиторий:** https://github.com/stiapanreha-dev/bdb.php
- **Сервер:** Ubuntu Server "moon" (176.117.212.121)
- **MS SQL Server:** 172.26.192.1:1433
  - **Базы данных:**
    - `business2025` (основная) - компании (db_companies) + закупки 2025
    - `business2020-2026` (по годам) - закупки соответствующих годов

## Миграция с SQLite на PostgreSQL

### Статус миграции

- **Локальная разработка:** ✅ Завершена (25.10.2025)
- **Продакшн:** ❌ Ожидается (требуется установка PostgreSQL на Windows Server)

### Что было сделано

1. Создана база данных `businessdb` в PostgreSQL
2. Выполнены все миграции Laravel
3. Создана команда `db:migrate-from-sqlite` для переноса данных
4. Успешно перенесены данные из SQLite в PostgreSQL (users, news, ideas, transactions)
5. Обновлена конфигурация `.env` для работы с PostgreSQL

### План для продакшн-сервера (Ubuntu)

1. **Установить PostgreSQL на Ubuntu Server:**
   ```bash
   sudo apt update
   sudo apt install postgresql postgresql-contrib
   sudo systemctl start postgresql
   sudo systemctl enable postgresql
   ```

2. **Создать базу данных и пользователя:**
   ```bash
   sudo -u postgres psql
   CREATE DATABASE businessdb;
   CREATE USER businessdb_user WITH ENCRYPTED PASSWORD 'your_secure_password';
   GRANT ALL PRIVILEGES ON DATABASE businessdb TO businessdb_user;
   \q
   ```

3. **Скопировать SQLite базу с продакшна локально:**
   ```bash
   scp XBMC:/home/alex/businessdb/database/database.sqlite ./prod-database.sqlite
   ```

4. **Обновить .env на продакшне:**
   ```bash
   ssh XBMC
   cd /home/alex/businessdb
   nano .env
   # Изменить DB_CONNECTION=sqlite на DB_CONNECTION=pgsql
   # Добавить настройки PostgreSQL
   ```

5. **Выполнить миграции и импорт данных:**
   ```bash
   cd /home/alex/businessdb
   php artisan migrate
   php artisan db:migrate-from-sqlite /home/alex/businessdb/database/database.sqlite
   ```

6. **Проверить работу приложения:**
   ```bash
   php artisan tinker --execute="echo 'Users: ' . \App\Models\User::count();"
   ```

7. **Создать бэкап SQLite и удалить файл:**
   ```bash
   cp database/database.sqlite database/database.sqlite.backup
   # После проверки работы можно удалить database.sqlite
   ```

---

## Платежная система ЮKassa

### Конфигурация

Добавлены ключи в `.env`:
```env
YOOKASSA_SHOP_ID=1197477
YOOKASSA_SECRET_KEY=test_bx4U_uV7ewcOpFZ_Xkm-0NZLAREJSr6KVHkj1jbp9PQ
```

Конфигурация в `config/services.php`:
```php
'yookassa' => [
    'shop_id' => env('YOOKASSA_SHOP_ID'),
    'secret_key' => env('YOOKASSA_SECRET_KEY'),
],
```

### Модели и миграции

**Таблица `payments` (PostgreSQL):**
- `id` - ID платежа
- `user_id` - ID пользователя (связь с users)
- `yookassa_payment_id` - ID платежа в системе ЮKassa (уникальный)
- `amount` - сумма платежа
- `currency` - валюта (по умолчанию RUB)
- `status` - статус (pending, succeeded, canceled)
- `payment_method` - способ оплаты (заполняется после оплаты)
- `description` - описание платежа
- `metadata` - дополнительные данные (JSON)
- `paid_at` - дата и время оплаты
- `created_at`, `updated_at` - временные метки

**Модель Payment:**
- Связь `belongsTo` с моделью User
- Helper-методы: `isSucceeded()`, `isPending()`, `isCanceled()`
- Cast для metadata (array), paid_at (datetime), amount (decimal:2)

### Контроллер PaymentController

**Основные методы:**

1. **create(Request $request)** - создание платежа
   - Валидация суммы и описания
   - Создание платежа через YooKassa API
   - Сохранение записи в БД
   - Перенаправление на страницу оплаты ЮKassa

2. **webhook(Request $request)** - обработка уведомлений от ЮKassa
   - Проверка типа уведомления (payment.succeeded)
   - Обновление статуса платежа
   - Начисление баланса пользователю
   - Создание транзакции

3. **callback(Request $request)** - возврат после оплаты
   - Перенаправление на страницу подписок с сообщением

4. **history()** - история платежей пользователя
   - Отображение списка платежей с пагинацией

5. **status($paymentId)** - проверка статуса платежа
   - Получение актуального статуса из API ЮKassa
   - Обновление данных в БД

### Маршруты

```php
// Авторизованные пользователи
Route::middleware(['auth'])->group(function () {
    Route::post('/payment/create', [PaymentController::class, 'create']);
    Route::get('/payment/callback', [PaymentController::class, 'callback']);
    Route::get('/payment/history', [PaymentController::class, 'history']);
    Route::get('/payment/status/{paymentId}', [PaymentController::class, 'status']);
});

// Webhook (публичный, без авторизации)
Route::post('/payment/webhook', [PaymentController::class, 'webhook']);
```

### Интеграция в интерфейс

**Страница подписок (`/subscriptions`):**
- Форма пополнения баланса
- Отображение текущего баланса
- Ссылка на историю платежей

**Страница истории (`/payment/history`):**
- Таблица со всеми платежами пользователя
- Статус платежа (цветные badges)
- Дата, сумма, способ оплаты, описание
- Пагинация

### Настройка webhook в ЮKassa

В личном кабинете ЮKassa необходимо настроить URL для уведомлений:

**Локально (для тестирования):**
- Использовать ngrok или локaltуннель для получения публичного URL
- URL webhook: `https://your-domain.ngrok.io/payment/webhook`

**Продакшн:**
- URL webhook: `https://businessdb.ru/payment/webhook`
- HTTP метод: POST
- Формат: JSON

### Тестирование

**Тестовые карты ЮKassa:**
- Успешная оплата: `5555 5555 5555 4477`
- Отклоненная оплата: `5555 5555 5555 5599`
- CVC: любые 3 цифры
- Срок действия: любая будущая дата
- Имя держателя: любое

**Проверка платежа:**
1. Авторизуйтесь на сайте
2. Перейдите на `/subscriptions`
3. Введите сумму пополнения и нажмите "Пополнить через ЮKassa"
4. Выполните тестовую оплату
5. После успешной оплаты баланс должен обновиться автоматически

**Проверка webhook:**
- Логи уведомлений: `storage/logs/laravel.log`
- Поиск: `Payment succeeded` или `YooKassa webhook error`

### Безопасность

- Webhook доступен без авторизации (требование ЮKassa)
- Проверка idempotency через уникальный ID платежа
- Защита от повторной обработки (проверка статуса перед начислением)
- Логирование всех операций с платежами

## Админ-панель

### Доступные страницы администратора

Все админ-страницы требуют роль `admin` и доступны через выпадающее меню "Админ".

1. **Управление пользователями** (`/admin/users`)
   - Просмотр всех пользователей
   - Изменение баланса пользователей
   - Назначение/снятие роли admin

2. **Модерация идей** (`/admin/ideas`)
   - Просмотр всех идей пользователей
   - Одобрение/отклонение/возврат идей на рассмотрение
   - Удаление идей

3. **Управление тарифами** (`/admin/tariffs`)
   - CRUD операции для тарифных планов
   - Настройка цен и длительности подписок

4. **Платежи ЮKassa** (`/admin/payments`) ⭐ NEW
   - Просмотр всех платежей с детальной информацией
   - Статистика по платежам (успешные, в ожидании, отменённые)
   - Фильтрация по статусу, пользователю и датам
   - Информация о способах оплаты и ID транзакций

5. **Статистика рассылок** (`/admin/newsletters`) ⭐ NEW
   - Общая статистика по всем рассылкам
   - Список всех рассылок пользователей с фильтрацией
   - История последних 50 отправок за 30 дней
   - Информация о ключевых словах и статусе подписок
   - Количество отправленных закупок в письмах

6. **Настройки рассылки** (`/admin/newsletter-settings`)
   - Управление автоматической рассылкой новостей
   - Настройка интервала отправки (10-1440 минут)
   - Управление автоматическим продлением подписок
   - Настройка времени продления подписок (UTC+3)

7. **SQL Запросы** (`/admin/sql`)
   - Выполнение SELECT запросов к любой базе данных
   - Выбор подключения (pgsql, mssql, mssql_2020-2026)
   - Ограничение: только SELECT, максимум 1000 строк, таймаут 10 сек

### Layout файл

**⚠️ ВАЖНО:** В проекте используется **единственный** layout файл:
- `resources/views/components/app-layout.blade.php` - используется через компонент `<x-app-layout>`

Все views используют компонент `<x-app-layout>`, а не директиву `@extends`.

## История изменений

### 2025-11-01 (вечер)
- ✅ Создана админ-страница "Платежи ЮKassa" (/admin/payments)
  - Статистика по платежам (успешные, в ожидании, отменённые)
  - Фильтрация по статусу, пользователю и датам
  - Детальная информация о каждом платеже (ID ЮKassa, способ оплаты, дата оплаты)
- ✅ Создана админ-страница "Статистика рассылок" (/admin/newsletters)
  - Общая статистика (всего рассылок, активных, отправлено сегодня)
  - Список всех рассылок с фильтрацией по статусу и пользователю
  - История последних 50 отправок за 30 дней
  - Информация о ключевых словах и подписках каждой рассылки
- ✅ Добавлены ссылки на новые админ-страницы в меню
- ✅ Удален неиспользуемый файл `resources/views/layouts/app.blade.php`
- ✅ Исправлена кодировка файлов (UTF-8)

### 2025-11-01 (утро)
- ✅ Интегрирован платежный шлюз ЮKassa
- ✅ Создана таблица payments для хранения информации о платежах
- ✅ Реализован PaymentController с обработкой платежей и webhook
- ✅ Добавлена форма пополнения баланса на странице подписок
- ✅ Создана страница истории платежей для пользователей
- ✅ Добавлена система управления настройками рассылки
- ✅ Исправлена кодировка файла newsletter-settings.blade.php

### 2025-10-31
- ✅ Реализован SQL admin panel (/admin/sql) с поддержкой всех баз данных
- ✅ Создана страница технической поддержки (/support)
- ✅ Оптимизация SQL Server: созданы скрипты для индексов на таблицах zakupki
- ✅ Исправлена ошибка в ZakupkiController (datetime conversion для SQL Server)
- ✅ Добавлена кнопка "Отменить" для возврата идей на рассмотрение
- ✅ Обновлен footer: ссылка /tariffs → /subscriptions
- ✅ Настроен автоматический деплой через GitHub Actions с очисткой opcache

### 2025-10-25
- ✅ Миграция с SQLite на PostgreSQL (локальная разработка)
- ✅ Настроен Devilbox с PostgreSQL 15
- ✅ Создана команда db:migrate-from-sqlite для переноса данных

---

**Последнее обновление:** 2025-11-01 23:50
