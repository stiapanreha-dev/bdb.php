# Инструкция по миграции на PostgreSQL (Продакшн)

## Статус
- Локальная разработка: ✅ **Завершена**
- Продакшн сервер: ⏳ **Требует установки PostgreSQL**

## Подготовка

База данных с продакшна скопирована локально:
- Файл: `prod-database.sqlite` (172 KB)
- Содержит актуальные данные пользователей, новостей и идей

## Шаги для установки на продакшн-сервере (moon)

### 1. Установка PostgreSQL

Подключитесь к серверу и выполните:

```bash
ssh XBMC
sudo apt update
sudo apt install -y postgresql postgresql-contrib php8.3-pgsql php8.3-pdo-pgsql
sudo systemctl enable postgresql
sudo systemctl start postgresql
```

Проверка установки:
```bash
psql --version
sudo systemctl status postgresql
```

### 2. Создание базы данных и пользователя

```bash
sudo -u postgres psql
```

В psql выполните:
```sql
CREATE DATABASE businessdb;
CREATE USER businessdb_user WITH ENCRYPTED PASSWORD 'СГЕНЕРИРУЙТЕ_НАДЕЖНЫЙ_ПАРОЛЬ';
GRANT ALL PRIVILEGES ON DATABASE businessdb TO businessdb_user;
\q
```

### 3. Настройка доступа

Если нужен доступ по TCP (не через unix socket), отредактируйте:

```bash
sudo nano /etc/postgresql/*/main/pg_hba.conf
```

Добавьте строку:
```
local   businessdb      businessdb_user                         md5
```

Перезапустите PostgreSQL:
```bash
sudo systemctl restart postgresql
```

### 4. Установка PHP расширений (если еще не установлены)

```bash
php -m | grep pgsql
php -m | grep pdo_pgsql
```

Если расширений нет:
```bash
sudo apt install -y php8.3-pgsql php8.3-pdo-pgsql
sudo systemctl restart php8.3-fpm
```

### 5. Обновление .env на сервере

```bash
cd /home/alex/businessdb
nano .env
```

Измените строки:
```env
# Было:
DB_CONNECTION=sqlite

# Стало:
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=businessdb
DB_USERNAME=businessdb_user
DB_PASSWORD=ВАШ_ПАРОЛЬ_ИЗ_ШАГА_2
```

### 6. Деплой команды миграции

Команда `db:migrate-from-sqlite` уже в репозитории. Обновите код:

```bash
cd /home/alex/businessdb
git pull origin main
```

### 7. Выполнение миграций Laravel

```bash
cd /home/alex/businessdb
php artisan config:clear
php artisan migrate
```

Проверьте что все таблицы созданы:
```bash
php artisan migrate:status
```

### 8. Импорт данных из SQLite

```bash
cd /home/alex/businessdb
php artisan db:migrate-from-sqlite /home/alex/businessdb/database/database.sqlite
```

Команда перенесет данные из таблиц:
- users
- news
- ideas
- transactions

### 9. Проверка данных

```bash
php artisan tinker --execute="
echo 'Users: ' . \App\Models\User::count() . PHP_EOL;
echo 'News: ' . \App\Models\News::count() . PHP_EOL;
echo 'Ideas: ' . \App\Models\Idea::count() . PHP_EOL;
echo 'Transactions: ' . \App\Models\Transaction::count() . PHP_EOL;
"
```

### 10. Тестирование приложения

Откройте https://businessdb.ru и проверьте:
- ✅ Вход в систему работает
- ✅ Профиль пользователя отображается корректно
- ✅ Новости загружаются
- ✅ Идеи отображаются
- ✅ Админ-панель доступна

### 11. Создание бэкапа SQLite

После успешной проверки:

```bash
cd /home/alex/businessdb/database
cp database.sqlite database.sqlite.backup_$(date +%Y%m%d)
# Опционально можно удалить database.sqlite после проверки
```

### 12. Перезапуск сервисов

```bash
sudo systemctl restart php8.3-fpm
sudo systemctl reload nginx
```

## Откат в случае проблем

Если что-то пошло не так:

```bash
cd /home/alex/businessdb
nano .env
# Верните DB_CONNECTION=sqlite
php artisan config:clear
sudo systemctl restart php8.3-fpm
```

## Мониторинг и логи

Логи PostgreSQL:
```bash
sudo tail -f /var/log/postgresql/postgresql-*-main.log
```

Логи Laravel:
```bash
tail -f /home/alex/businessdb/storage/logs/laravel.log
```

Логи Nginx:
```bash
sudo tail -f /var/log/nginx/businessdb-error.log
```

## Контакты для вопросов

- Документация проекта: `/home/alex/businessdb/CLAUDE.md`
- Git: https://github.com/stiapanreha-dev/bdb.php
