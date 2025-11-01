# Реализация сервиса рассылок - Оставшиеся задачи

## ✅ Выполнено

1. Создана миграция для таблиц: `newsletters`, `newsletter_keywords`, `newsletter_logs`
2. Созданы модели: `Newsletter`, `NewsletterKeyword`, `NewsletterLog`
3. Создан класс экспорта: `NewsletterExport`
4. Миграции выполнены успешно

## ⚠️ Проблемы с правами доступа

Файлы создаются от root через Docker. Нужно отредактировать вручную или через SSH на сервере:

### 1. app/Mail/NewsletterMail.php

Заменить содержимое на:

```php
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewsletterMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $userName;
    public int $zakupkiCount;
    public string $period;
    public string $filePath;

    public function __construct(
        string $userName,
        int $zakupkiCount,
        string $period,
        string $filePath
    ) {
        $this->userName = $userName;
        $this->zakupkiCount = $zakupkiCount;
        $this->period = $period;
        $this->filePath = $filePath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Рассылка закупок BusinessDB - ' . $this->period,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.newsletter',
            with: [
                'userName' => $this->userName,
                'zakupkiCount' => $this->zakupkiCount,
                'period' => $this->period,
            ]
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->filePath)
                ->as('zakupki_' . date('Y-m-d_H-i') . '.xlsx')
                ->withMime('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'),
        ];
    }
}
```

### 2. app/Console/Commands/SendNewsletters.php

Заменить содержимое на: [см. файл ниже - очень длинный код, уже создан но нужно отредактировать]

## 📋 TODO: Остальные задачи

### 3. Создать контроллер NewsletterController

```bash
php artisan make:controller NewsletterController
```

Методы:
- `index()` - показать интерфейс управления рассылками
- `store()` - создать/обновить рассылку
- `updateKeywords()` - обновить ключевые слова
- `toggle()` - включить/выключить рассылку
- `destroy()` - удалить рассылку

### 4. Создать view для управления рассылками

Файл: `resources/views/newsletters/index.blade.php`

Интерфейс должен содержать:
- Форма активации рассылки (чекбокс)
- Поле для дополнительного email (опционально)
- Textarea для ввода ключевых слов (каждая строка = набор ключевых слов через пробел или запятую)
- История отправленных рассылок (последние 10)
- Статистика: количество найденных закупок, дата последней отправки

### 5. Создать email template

Файл: `resources/views/emails/newsletter.blade.php`

Содержание:
```blade
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Рассылка закупок BusinessDB</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #4CAF50;">Рассылка закупок BusinessDB</h2>

        <p>Здравствуйте, {{ $userName }}!</p>

        <p>За период <strong>{{ $period }}</strong> найдено <strong>{{ $zakupkiCount }}</strong> закупок по вашим ключевым словам.</p>

        @if($zakupkiCount > 0)
            <p>Полный список закупок во вложении (Excel файл).</p>
        @else
            <p>По вашим запросам ничего не найдено за этот период.</p>
        @endif

        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">

        <p style="font-size: 12px; color: #666;">
            Это автоматическая рассылка от сервиса BusinessDB.<br>
            Вы можете управлять настройками рассылки в <a href="https://businessdb.ru/newsletters">личном кабинете</a>.
        </p>
    </div>
</body>
</html>
```

### 6. Добавить роуты

Файл: `routes/web.php`

```php
// Newsletters routes (для авторизованных пользователей)
Route::middleware('auth')->group(function () {
    Route::get('/newsletters', [NewsletterController::class, 'index'])->name('newsletters.index');
    Route::post('/newsletters', [NewsletterController::class, 'store'])->name('newsletters.store');
    Route::post('/newsletters/keywords', [NewsletterController::class, 'updateKeywords'])->name('newsletters.keywords');
    Route::post('/newsletters/toggle', [NewsletterController::class, 'toggle'])->name('newsletters.toggle');
    Route::delete('/newsletters', [NewsletterController::class, 'destroy'])->name('newsletters.destroy');
});
```

### 7. Настроить расписание

Файл: `app/Console/Kernel.php`

Добавить в метод `schedule()`:

```php
// Отправка рассылок каждые 3 часа
$schedule->command('newsletters:send')
    ->everyThreeHours()
    ->withoutOverlapping()
    ->runInBackground();
```

### 8. Добавить ссылку в навигацию

Файл: `resources/views/layouts/app.blade.php`

Добавить ссылку в навигационное меню:

```blade
@auth
    <a href="{{ route('newsletters.index') }}"
       class="text-gray-700 hover:text-green-600 px-3 py-2 rounded-md text-sm font-medium">
        Рассылки
    </a>
@endauth
```

### 9. Админ-панель: управление подписками

Файл: `resources/views/admin/newsletters.blade.php`

Добавить раздел в админку для:
- Просмотра всех подписок
- Включение/выключение подписок пользователей
- Просмотра логов отправки
- Ручной запуск рассылки для конкретного пользователя

### 10. Обновить модель User

Добавить связь в `app/Models/User.php`:

```php
public function newsletter(): HasOne
{
    return $this->hasOne(Newsletter::class);
}

public function hasNewsletterSubscription(): bool
{
    return $this->newsletter && $this->newsletter->is_active;
}
```

### 11. Настроить cron на сервере

На продакшн-сервере добавить в crontab:

```bash
# Открыть crontab
crontab -e

# Добавить строку:
* * * * * cd /home/alex/businessdb && php artisan schedule:run >> /dev/null 2>&1
```

### 12. Тестирование

```bash
# Локально: тестовая отправка для конкретного пользователя
php artisan newsletters:send --user_id=1

# Проверка расписания
php artisan schedule:list

# Ручной запуск для всех
php artisan newsletters:send
```

## 💰 Монетизация

Добавить проверку подписки пользователя:
- Базовая подписка: НЕ имеет доступа к рассылкам
- Премиум подписка (+500р/месяц): имеет доступ к рассылкам

Обновить проверку в контроллере:

```php
public function index()
{
    if (!Auth::user()->hasNewsletterAccess()) {
        return view('newsletters.upsell'); // Страница с предложением купить подписку
    }

    // ... остальной код
}
```

Добавить метод в модель User:

```php
public function hasNewsletterAccess(): bool
{
    // Проверка наличия активной подписки с доступом к рассылкам
    return $this->isAdmin() || $this->hasActiveSubscription();
}
```

## 📊 Дополнительные улучшения (опционально)

1. **Лимиты:**
   - Максимум 10 наборов ключевых слов на пользователя
   - Максимум 1000 закупок на рассылку (уже реализовано в команде)

2. **Уведомления:**
   - Уведомление в интерфейсе о новых найденных закупках
   - Push-уведомления (опционально)

3. **Аналитика:**
   - График количества найденных закупок по дням
   - ТОП-10 самых популярных ключевых слов
   - Статистика открытия писем (через tracking pixel)

4. **Фильтры:**
   - Минимальная стоимость закупки для рассылки
   - Исключение определенных заказчиков
   - Регионы (города)

## 🔧 Настройка email на сервере

Убедиться что в `.env` настроен SMTP:

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

## 🎯 Приоритеты реализации

1. **Высокий:**
   - Отредактировать SendNewsletters.php и NewsletterMail.php (проблема с правами)
   - Создать NewsletterController
   - Создать view newsletters/index.blade.php
   - Создать email template
   - Добавить роуты
   - Настроить расписание

2. **Средний:**
   - Админ-панель для управления
   - Обновить навигацию
   - Добавить связь в User модели

3. **Низкий:**
   - Дополнительные улучшения
   - Аналитика
   - Фильтры
