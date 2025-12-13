<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ваш сайт одобрен</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #28a745;">Ваш сайт одобрен!</h2>

        <p>Здравствуйте!</p>

        <p>Рады сообщить, что ваш сайт <strong>{{ $site->name }}</strong> успешно прошел модерацию и теперь доступен в каталоге сайтов Business Database.</p>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0;"><strong>Название:</strong> {{ $site->name }}</p>
            <p style="margin: 10px 0 0 0;"><strong>URL:</strong> <a href="{{ $site->url }}">{{ $site->url }}</a></p>
            @if($site->category)
            <p style="margin: 10px 0 0 0;"><strong>Категория:</strong> {{ $site->category->name }}</p>
            @endif
        </div>

        @if($site->moderation_comment)
        <p><strong>Комментарий модератора:</strong></p>
        <p style="background-color: #e7f5ea; padding: 10px; border-radius: 5px;">{{ $site->moderation_comment }}</p>
        @endif

        <p>Страница вашего сайта в каталоге: <a href="{{ url('/sites/' . $site->slug) }}">{{ url('/sites/' . $site->slug) }}</a></p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">

        <p style="font-size: 12px; color: #666;">
            Это автоматическое письмо сервиса Business Database.<br>
            <a href="https://businessdb.ru">https://businessdb.ru</a>
        </p>
    </div>
</body>
</html>
