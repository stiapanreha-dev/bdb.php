# BusinessDB

Laravel 12 приложение для работы с базой данных компаний и закупок.

**URL:** https://businessdb.ru/

## Быстрый старт

```bash
# SSH на сервер
ssh XBMC

# Деплой
ssh XBMC "cd /home/alex/businessdb && git pull origin main && php artisan optimize:clear"

# Локально через Docker
docker exec devilbox-php83-1 php /shared/httpd/businessdb/artisan [command]
```

## Документация

| Файл | Описание |
|------|----------|
| [docs/DEPLOY.md](docs/DEPLOY.md) | Деплой, окружения, SSH, команды |
| [docs/DATABASE.md](docs/DATABASE.md) | Подключения к БД, таблицы, примеры |
| [docs/ARCHITECTURE.md](docs/ARCHITECTURE.md) | Контроллеры, модели, структура проекта |
| [docs/FEATURES.md](docs/FEATURES.md) | Магазин, платежи, модули, админка |

## Правила для Claude Code

1. **НЕ выполнять `git commit` и `git push` без явной команды**
2. При коммитах использовать шаблон с `🤖 Generated with Claude Code` и `Co-Authored-By: Claude`
3. Использовать `<x-app-layout>` для views (единственный layout)
4. Middleware определять в routes, не в конструкторах контроллеров

## Ключевые технологии

- **Backend:** Laravel 12, PHP 8.3, PostgreSQL, MS SQL Server
- **Frontend:** Blade, Tailwind CSS, Alpine.js, Vite
- **Особенности:** Editor.js для контента, ЮKassa для платежей, система модулей

## Часто используемые пути

```
app/Http/Controllers/           # Контроллеры
app/Http/Controllers/Admin/     # Админ контроллеры
app/Models/                     # Модели
resources/views/                # Views
resources/views/components/app-layout.blade.php  # Главный layout
routes/web.php                  # Роуты
```

## Troubleshooting

**MS SQL драйвер:**
```bash
sudo apt install php8.3-sqlsrv php8.3-pdo-sqlsrv
```

**Middleware ошибка:**
Убрать `$this->middleware()` из конструктора, использовать в routes/web.php

**504 Timeout:**
Создать индексы: `database/sql/create_zakupki_indexes.sql`
