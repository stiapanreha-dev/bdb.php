# Деплой и окружения

## Локальная разработка (Devilbox)

**Путь:** `/home/lexun/devilbox/data/www/businessdb`
**URL:** https://businessdb.dvl.to/

**Docker контейнеры:**
- devilbox-php83-1 (PHP 8.3)
- devilbox-pgsql-1 (PostgreSQL 15)

**База данных PostgreSQL:**
- Host: pgsql
- Port: 5432
- Database: businessdb
- Username: postgres
- Password: (пустой)

## Продакшн (Ubuntu Server)

**Сервер:** moon (176.117.212.121)
**SSH:** `ssh XBMC` (настроен в ~/.ssh/config)
**SSH ключ:** ~/.ssh/id_smtb
**OS:** Ubuntu (Linux 6.8.0-86-generic)
**Путь проекта:** /home/alex/businessdb
**Пользователь:** alex
**Группа:** www-data
**Веб-сервер:** Nginx 1.24.0
**PHP:** 8.3.6

**Nginx конфиг:** `/etc/nginx/sites-available/businessdb.ru`

## Git workflow

```bash
# Локальные изменения
cd /home/lexun/devilbox/data/www/businessdb
git add .
git commit -m "Описание"
git push origin main

# Деплой на продакшн
ssh XBMC "cd /home/alex/businessdb && git pull origin main && php artisan optimize:clear"
```

## Автоматический деплой (GitHub Actions)

При push в main:
1. GitHub Actions подключается по SSH
2. Выполняет `git pull origin main`
3. Выполняет `php artisan optimize:clear`
4. Перезапускает PHP-FPM

**Файл:** `.github/workflows/deploy.yml`

**Проверка:**
```bash
gh run list --limit 5
gh run view [run_id]
```

## Полезные команды

```bash
# SSH
ssh XBMC

# Очистка кеша
php artisan optimize:clear

# Миграции
php artisan migrate --force

# Рассылки
php artisan newsletters:send
php artisan newsletters:renew
```

## PHP расширения (Ubuntu)

```bash
sudo apt install php8.3-{cli,fpm,pgsql,pdo,sqlite3,gd,zip,mbstring,xml,curl,sqlsrv,pdo-sqlsrv}
```
