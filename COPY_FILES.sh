#!/bin/bash

# Скрипт для копирования файлов из temp_newsletter_files
# Запускать на продакшн-сервере после деплоя

echo "Copying newsletter files..."

# Проверка существования temp директории
if [ ! -d "temp_newsletter_files" ]; then
    echo "Error: temp_newsletter_files directory not found!"
    exit 1
fi

# Копирование SendNewsletters.php
if [ -f "temp_newsletter_files/SendNewsletters.php" ]; then
    cp temp_newsletter_files/SendNewsletters.php app/Console/Commands/SendNewsletters.php
    echo "✓ Copied SendNewsletters.php"
else
    echo "✗ SendNewsletters.php not found"
fi

# Копирование NewsletterMail.php
if [ -f "temp_newsletter_files/NewsletterMail.php" ]; then
    cp temp_newsletter_files/NewsletterMail.php app/Mail/NewsletterMail.php
    echo "✓ Copied NewsletterMail.php"
else
    echo "✗ NewsletterMail.php not found"
fi

# Копирование NewsletterController.php
if [ -f "temp_newsletter_files/NewsletterController.php" ]; then
    cp temp_newsletter_files/NewsletterController.php app/Http/Controllers/NewsletterController.php
    echo "✓ Copied NewsletterController.php"
else
    echo "✗ NewsletterController.php not found"
fi

# Установка правильных прав доступа
chmod 644 app/Console/Commands/SendNewsletters.php
chmod 644 app/Mail/NewsletterMail.php
chmod 644 app/Http/Controllers/NewsletterController.php

echo ""
echo "Done! Files copied successfully."
echo ""
echo "Next steps:"
echo "1. Run: php artisan migrate (if not done yet)"
echo "2. Test: php artisan newsletters:send --user_id=1"
echo "3. Check schedule: php artisan schedule:list"
