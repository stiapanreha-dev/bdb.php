# BusinessDB - Документация для разработки

## Обзор проекта

Laravel 11 приложение для работы с базой данных компаний и закупок.

**URL продакшена:** https://businessdb.ru/

## Архитектура

### Стек технологий
- **Framework:** Laravel 11.x
- **PHP:** 8.2+ (локально 8.3, продакшн 8.2)
- **База данных:** Microsoft SQL Server (2 подключения: `mssql` и `mssql_cp1251`)
- **Frontend:** Blade templates, Tailwind CSS, Alpine.js
- **Экспорт:** Maatwebsite Excel (для экспорта в .xlsx)

### Подключения к БД

В проекте используется 2 подключения к MS SQL Server:

1. **mssql** - основное подключение (UTF-8)
2. **mssql_cp1251** - для работы с VARCHAR полями в кодировке CP1251

Пример использования:
```php
// Для текстовых полей на русском используем cp1251
DB::connection('mssql_cp1251')->table('db_companies')...

// Для обычных запросов
DB::connection('mssql')->table('zakupki')...
```

## Окружения

### Локальная разработка (Devilbox)

**Путь:** `/home/lexun/devilbox/data/www/businessdb`
**URL:** https://businessdb.dvl.to/
**Docker контейнер:** devilbox-php83-1

### Продакшн (Windows Server)

**Сервер:** 176.117.212.121
**SSH пользователь:** lexun
**SSH ключ:** ~/.ssh/id_smtb
**Путь проекта:** C:/soft/bussiness.db2/bdb.php
**Веб-сервер:** XAMPP Apache 2.4.58
**PHP:** 8.2.12 Thread Safe x64

#### Настройки Apache

**VirtualHost конфигурация:**
- HTTP (80): `C:\xampp\apache\conf\extra\httpd-vhosts.conf` - редирект на HTTPS
- HTTPS (443): `C:\xampp\apache\conf\extra\httpd-ssl.conf` - основная конфигурация

Важные директивы в SSL VirtualHost:
```apache
<VirtualHost *:443>
    ServerName businessdb.ru
    ServerAlias www.businessdb.ru
    DocumentRoot "C:/soft/bussiness.db2/bdb.php/public"

    SSLEngine on
    SSLCertificateFile "C:/xampp/apache/conf/ssl.crt/certificate.crt"
    SSLCertificateKeyFile "C:/xampp/apache/conf/ssl.key/private.key"

    <Directory "C:/soft/bussiness.db2/bdb.php/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

#### Необходимые PHP расширения

В `C:\xampp\php\php.ini` должны быть включены:
```ini
extension=gd
extension=zip
extension=sqlsrv
extension=pdo_sqlsrv
```

**Расположение драйверов SQL Server:**
- `C:\xampp\php\ext\php_sqlsrv.dll`
- `C:\xampp\php\ext\php_pdo_sqlsrv.dll`

Драйверы взяты из `Windows_5.12.0RTW.zip` (PHP 8.2 Thread Safe x64):
- Источник: Microsoft Drivers for PHP for SQL Server v5.12.0
- Файлы: `php_sqlsrv_82_ts_x64.dll`, `php_pdo_sqlsrv_82_ts_x64.dll`

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
   ssh -i ~/.ssh/id_smtb lexun@176.117.212.121
   cd C:\soft\bussiness.db2\bdb.php
   git pull origin main
   ```

3. **Если нужны composer зависимости:**
   ```bash
   C:\xampp\php\php.exe composer.phar install --no-dev --optimize-autoloader
   ```

4. **Перезапуск Apache:**
   - Через XAMPP Control Panel: `C:\xampp\xampp-control.exe`
   - Или через PowerShell:
     ```powershell
     Stop-Process -Name httpd -Force
     Start-Process C:\xampp\apache\bin\httpd.exe -WindowStyle Hidden
     ```

### Важно при деплое

- **ВСЕГДА** используйте git для изменений, НЕ редактируйте файлы напрямую на сервере
- При коммитах **НЕ** добавляйте упоминания Claude/AI в сообщения
- Проверяйте `.env` файл на сервере - он не версионируется
- После изменений в `composer.json` не забудьте выполнить `composer install` на сервере

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
- `CompanyController` - работа с компаниями
- `AdminController` - админ-панель (управление пользователями, идеями)
- `ProfileController` - профиль пользователя (из Laravel Breeze)
- `NewsController` - новости
- `IdeasController` - идеи пользователей

### Helpers

- `DataMaskingHelper` - маскирование email, телефонов, сайтов для пользователей без баланса

### Exports

- `CompaniesExport` - экспорт компаний в Excel

### База данных (MS SQL Server)

**Основные таблицы:**
- `db_companies` - компании
- `db_rubrics` - рубрики
- `db_subrubrics` - подрубрики
- `db_cities` - города
- `zakupki` - закупки

**Локальные таблицы Laravel (SQLite в разработке):**
- `users` - пользователи
- `ideas` - идеи пользователей
- `news` - новости

## Конфигурация окружения

### .env (продакшн)

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://businessdb.ru

DB_CONNECTION=sqlite
# SQLite для локальных таблиц Laravel

# MS SQL Server подключения (для внешних данных)
MSSQL_HOST=172.26.192.1
MSSQL_PORT=1433
MSSQL_DATABASE=buss
MSSQL_USERNAME=sa
MSSQL_PASSWORD=***
```

## Troubleshooting

### Проблема: "could not find driver" для MS SQL

**Решение:**
1. Проверьте наличие DLL файлов в `C:\xampp\php\ext\`
2. Убедитесь что в `php.ini` включены `extension=sqlsrv` и `extension=pdo_sqlsrv`
3. Перезапустите Apache

### Проблема: "Call to undefined method middleware()"

**Решение:**
Удалите конструктор `__construct()` с вызовом `$this->middleware()` из контроллера.
Middleware должен быть определен в routes/web.php.

### Проблема: Apache не стартует

**Диагностика:**
```bash
C:\xampp\apache\bin\httpd.exe -t  # Проверка синтаксиса
```

Проверьте логи:
- `C:\xampp\apache\logs\error.log`
- `C:\xampp\apache\logs\businessdb-ssl-error.log`

### Проблема: Ошибка доступа к файлам (403 Forbidden)

**Решение:**
Проверьте наличие секции `<Directory>` в httpd-ssl.conf с правильными правами:
```apache
<Directory "C:/soft/bussiness.db2/bdb.php/public">
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
</Directory>
```

## Полезные команды

### SSH подключение к серверу
```bash
ssh -i ~/.ssh/id_smtb lexun@176.117.212.121
```

### Проверка PHP модулей на сервере
```bash
C:\xampp\php\php.exe -m
```

### Проверка git статуса на сервере
```bash
cd C:\soft\bussiness.db2\bdb.php
git status
git log --oneline -5
```

### Очистка кеша Laravel
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

## Контакты и ресурсы

- **Git репозиторий:** https://github.com/stiapanreha-dev/bdb.php
- **Сервер:** Windows Server 10 (176.117.212.121)
- **MS SQL Server:** 172.26.192.1:1433

---

**Последнее обновление:** 2025-10-14
