# Настройка сервиса рассылок - Финальные шаги

## ✅ Что уже сделано

1. ✅ Созданы миграции (newsletters, newsletter_keywords, newsletter_logs)
2. ✅ Созданы модели (Newsletter, NewsletterKeyword, NewsletterLog)
3. ✅ Создан NewsletterExport для экспорта в Excel
4. ✅ Созданы view (newsletters/index.blade.php, emails/newsletter.blade.php)
5. ✅ Добавлены роуты в routes/web.php
6. ✅ Настроено расписание в bootstrap/app.php
7. ✅ Обновлена навигация
8. ✅ Добавлены методы в модель User

## 📋 Что нужно сделать на сервере

### 1. Деплой кода

```bash
# На локальной машине
git add .
git commit -m "Добавлен сервис рассылок по ключевым словам

🤖 Generated with Claude Code

Co-Authored-By: Claude <noreply@anthropic.com>"
git push origin main

# На продакшн-сервере
ssh XBMC
cd /home/alex/businessdb
git pull origin main
```

### 2. Копирование файлов из temp директории

Из-за проблем с правами доступа в Docker, некоторые файлы были созданы в `temp_newsletter_files/`:

```bash
cd /home/alex/businessdb
bash COPY_FILES.sh
```

Это скопирует:
- `SendNewsletters.php` → `app/Console/Commands/`
- `NewsletterMail.php` → `app/Mail/`
- `NewsletterController.php` → `app/Http/Controllers/`

### 3. Выполнение миграций

```bash
php artisan migrate
```

Будут созданы таблицы:
- `newsletters`
- `newsletter_keywords`
- `newsletter_logs`

### 4. Настройка cron (ВАЖНО!)

Для автоматической отправки рассылок каждые 3 часа:

```bash
crontab -e
```

Добавьте строку:

```
* * * * * cd /home/alex/businessdb && php artisan schedule:run >> /dev/null 2>&1
```

Проверка:

```bash
php artisan schedule:list
```

Вы должны увидеть:

```
newsletters:send .......... Every 3 hours ..... Next Due: ...
```

### 5. Настройка email (если еще не настроен)

В `.env` должны быть настроены параметры SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.yandex.ru
MAIL_PORT=465
MAIL_USERNAME=no-reply@businessdb.ru
MAIL_PASSWORD=***
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS=no-reply@businessdb.ru
MAIL_FROM_NAME="BusinessDB"
```

### 6. Создание директории для temp файлов

```bash
mkdir -p storage/app/temp
chmod 755 storage/app/temp
```

### 7. Очистка кеша

```bash
php artisan optimize:clear
sudo systemctl restart php8.3-fpm
```

## 🧪 Тестирование

### Тестовая отправка рассылки

```bash
# Для конкретного пользователя (замените 1 на ID пользователя)
php artisan newsletters:send --user_id=1
```

### Проверка логов

```bash
tail -f storage/logs/laravel.log
```

### Проверка базы данных

```bash
php artisan tinker
```

```php
// Проверка таблиц
\App\Models\Newsletter::count();
\App\Models\NewsletterKeyword::count();
\App\Models\NewsletterLog::count();

// Создание тестовой рассылки
$newsletter = \App\Models\Newsletter::create([
    'user_id' => 1,
    'is_active' => true,
]);

$newsletter->keywords()->create([
    'keywords' => 'программное обеспечение microsoft'
]);

// Тестовая отправка
\Artisan::call('newsletters:send', ['--user_id' => 1]);
```

## 📱 Использование

### Для пользователей:

1. Перейти на страницу **"Рассылки"** в меню (только для авторизованных)
2. Включить рассылку (чекбокс "Включить рассылку")
3. Опционально: указать дополнительный email
4. Добавить ключевые слова (каждая строка = набор ключевых слов)
5. Нажать "Сохранить"

### Формат ключевых слов:

Каждая строка - отдельный набор ключевых слов для OR запроса:
```
программное обеспечение microsoft
компьютеры ноутбуки принтеры
мебель офисная
```

Это будет искать закупки содержащие:
- "программное" OR "обеспечение" OR "microsoft"
- "компьютеры" OR "ноутбуки" OR "принтеры"
- "мебель" OR "офисная"

### Когда отправляются рассылки:

- Каждые 3 часа (00:00, 03:00, 06:00, 09:00, 12:00, 15:00, 18:00, 21:00)
- Только для активных подписок (`is_active = true`)
- Поиск закупок с момента последней рассылки
- Если закупок нет - письмо не отправляется (но `last_sent_at` обновляется)

### Что получает пользователь:

- Email с количеством найденных закупок
- Excel файл с детальной информацией:
  - Дата создания
  - Предмет закупки
  - Начальная цена
  - Заказчик
  - Email, телефон
  - Адрес
  - Тип закупки

## 🔧 Troubleshooting

### Рассылки не отправляются

1. Проверьте cron:
   ```bash
   crontab -l
   php artisan schedule:list
   ```

2. Проверьте логи:
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. Проверьте настройки email:
   ```bash
   php artisan tinker
   Mail::raw('Test', function($message) {
       $message->to('your@email.com')->subject('Test');
   });
   ```

### Ошибка "Class 'Newsletter' not found"

```bash
composer dump-autoload
php artisan optimize:clear
```

### Ошибка прав доступа к storage

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
chown -R alex:www-data storage bootstrap/cache
```

### Email не отправляется

1. Проверьте `.env` настройки MAIL_*
2. Проверьте что SMTP порт открыт:
   ```bash
   telnet smtp.yandex.ru 465
   ```
3. Проверьте логи email:
   ```bash
   grep -i "mail" storage/logs/laravel.log
   ```

## 📊 Мониторинг

### Статистика по рассылкам

```sql
-- SQL Admin Panel (https://businessdb.ru/admin/sql)

-- Общее количество подписок
SELECT COUNT(*) as total_newsletters,
       SUM(CASE WHEN is_active THEN 1 ELSE 0 END) as active_newsletters
FROM newsletters;

-- История отправок за последние 7 дней
SELECT DATE(sent_at) as date,
       COUNT(*) as emails_sent,
       SUM(zakupki_count) as total_zakupki,
       SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as successful,
       SUM(CASE WHEN status = 'failed' THEN 1 ELSE 0 END) as failed
FROM newsletter_logs
WHERE sent_at >= NOW() - INTERVAL '7 days'
GROUP BY DATE(sent_at)
ORDER BY date DESC;

-- Самые популярные ключевые слова
SELECT keywords, COUNT(*) as usage_count
FROM newsletter_keywords
GROUP BY keywords
ORDER BY usage_count DESC
LIMIT 10;
```

### Logs

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# Nginx logs
sudo tail -f /var/log/nginx/businessdb-error.log
sudo tail -f /var/log/nginx/businessdb-access.log
```

## 🎯 Что дальше (опционально)

1. **Админ-панель для управления рассылками:**
   - Просмотр всех подписок
   - Ручной запуск для конкретного пользователя
   - Статистика

2. **Ограничения:**
   - Максимум 10 наборов ключевых слов на пользователя
   - Лимит на количество закупок в одной рассылке (сейчас 1000)

3. **Дополнительные фильтры:**
   - Минимальная стоимость закупки
   - Регионы (города)
   - Исключение определенных заказчиков

4. **Уведомления:**
   - Push-уведомления о новых закупках
   - Уведомления в интерфейсе

## ✅ Checklist

- [ ] Код задеплоен на сервер
- [ ] Выполнен COPY_FILES.sh
- [ ] Миграции выполнены
- [ ] Cron настроен
- [ ] Email SMTP настроен
- [ ] Директория storage/app/temp создана
- [ ] Кеш очищен
- [ ] Тестовая рассылка отправлена успешно
- [ ] Проверена страница /newsletters в браузере
- [ ] Проверены логи

## 📞 Поддержка

- Telegram: @cdvks
- Email: support@businessdb.ru
