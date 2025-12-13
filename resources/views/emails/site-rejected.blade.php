<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Сайт не прошел модерацию</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #dc3545;">Сайт не прошел модерацию</h2>

        <p>Здравствуйте!</p>

        <p>К сожалению, ваш сайт <strong>{{ $site->name }}</strong> не прошел модерацию в каталоге Business Database.</p>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0;"><strong>Название:</strong> {{ $site->name }}</p>
            <p style="margin: 10px 0 0 0;"><strong>URL:</strong> {{ $site->url }}</p>
        </div>

        <p><strong>Причина отклонения:</strong></p>
        <div style="background-color: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;">
            {{ $reason }}
        </div>

        <p style="margin-top: 20px;">Вы можете исправить указанные замечания и повторно отправить заявку на добавление сайта в каталог.</p>

        <p><a href="{{ url('/sites/create') }}" style="display: inline-block; padding: 10px 20px; background-color: #0d6efd; color: #fff; text-decoration: none; border-radius: 5px;">Добавить сайт заново</a></p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">

        <p style="font-size: 12px; color: #666;">
            Это автоматическое письмо сервиса Business Database.<br>
            <a href="https://businessdb.ru">https://businessdb.ru</a>
        </p>
    </div>
</body>
</html>
