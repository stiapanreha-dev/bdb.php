<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Рассылка закупок BusinessDB</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            background-color: #4CAF50;
            color: white;
            padding: 30px 20px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px 20px;
        }
        .content p {
            margin: 0 0 15px 0;
        }
        .stats {
            background-color: #f9f9f9;
            border-left: 4px solid #4CAF50;
            padding: 15px;
            margin: 20px 0;
        }
        .stats strong {
            color: #4CAF50;
            font-size: 20px;
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #4CAF50;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            margin: 20px 0;
        }
        .footer {
            background-color: #f9f9f9;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #ddd;
        }
        .footer a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📧 Рассылка закупок BusinessDB</h1>
        </div>

        <div class="content">
            <p>Здравствуйте, <strong>{{ $userName }}</strong>!</p>

            <p>За период <strong>{{ $period }}</strong> найдено новых закупок по вашим ключевым словам.</p>

            <div class="stats">
                <p style="margin: 0;">Найдено закупок: <strong>{{ $zakupkiCount }}</strong></p>
            </div>

            @if($zakupkiCount > 0)
                <p>✅ Полный список закупок во вложении (Excel файл).</p>

                <p>В файле вы найдете:</p>
                <ul>
                    <li>Дата создания закупки</li>
                    <li>Предмет закупки</li>
                    <li>Начальная цена</li>
                    <li>Заказчик</li>
                    <li>Контактные данные (email, телефон)</li>
                    <li>Адрес</li>
                    <li>Тип закупки</li>
                </ul>

                <a href="https://businessdb.ru/zakupki" class="button">Перейти на сайт</a>
            @else
                <p>ℹ️ По вашим запросам ничего не найдено за этот период.</p>
                <p>Попробуйте изменить ключевые слова или расширить критерии поиска.</p>

                <a href="https://businessdb.ru/newsletters" class="button">Изменить ключевые слова</a>
            @endif

            <p style="margin-top: 30px;">С уважением,<br>Команда BusinessDB</p>
        </div>

        <div class="footer">
            <p>Это автоматическая рассылка от сервиса <a href="https://businessdb.ru">BusinessDB.ru</a></p>
            <p>Вы можете управлять настройками рассылки в <a href="https://businessdb.ru/newsletters">личном кабинете</a></p>
            <p style="margin-top: 10px;">
                <a href="https://businessdb.ru/support">Поддержка</a> |
                <a href="https://businessdb.ru/privacy-policy">Политика конфиденциальности</a>
            </p>
        </div>
    </div>
</body>
</html>
