<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Рассылка закупок</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #0066cc;">Рассылка закупок по ключевым словам</h2>

        <p>Здравствуйте, {{ $userName }}!</p>

        <p>По вашим ключевым словам найдено <strong>{{ $zakupkiCount }}</strong> {{ $zakupkiCount == 1 ? 'закупка' : ($zakupkiCount < 5 ? 'закупки' : 'закупок') }}.</p>

        <p>Период: <strong>{{ $period }}</strong></p>

        <p>Все найденные закупки прикреплены к письму в формате Excel.</p>

        <hr style="border: none; border-top: 1px solid #ddd; margin: 20px 0;">

        <p style="font-size: 12px; color: #666;">
            Это автоматическое письмо сервиса Business database.<br>
            Для изменения настроек рассылки перейдите по ссылке: <a href="https://businessdb.ru/newsletters">https://businessdb.ru/newsletters</a>
        </p>
    </div>
</body>
</html>
