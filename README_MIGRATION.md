# 🚀 Business Database - Laravel Migration Plan

## ✅ Текущий статус установки

- ✅ Laravel 11.x установлен
- ✅ Laravel Breeze (Auth) установлен
- ✅ Maatwebsite Excel установлен
- ✅ Doctrine DBAL установлен
- ✅ Проект доступен: https://businessdb.dvl.to/
- ✅ База данных: SQLite (локальная)

---

## 📋 План миграции проекта

### Phase 1: Database & Models (День 1-2)

#### 1.1 SQLite Tables (Локальная БД)
```bash
php artisan make:migration create_users_table
php artisan make:migration create_transactions_table
php artisan make:migration create_news_table
php artisan make:migration create_ideas_table
php artisan make:migration create_email_verifications_table
```

#### 1.2 Models
```bash
php artisan make:model User
php artisan make:model Transaction
php artisan make:model News
php artisan make:model Idea
php artisan make:model EmailVerification
```

#### 1.3 MSSQL Connection
```php
// config/database.php - добавить MSSQL connection
'mssql' => [
    'driver' => 'sqlsrv',
    'host' => env('MSSQL_HOST'),
    'port' => env('MSSQL_PORT', 1433),
    'database' => env('MSSQL_DATABASE'),
    'username' => env('MSSQL_USER'),
    'password' => env('MSSQL_PASSWORD'),
    'charset' => 'utf8',
    'prefix' => '',
],
```

---

### Phase 2: Authentication & Verification (День 3-4)

#### 2.1 Controllers
```bash
php artisan make:controller Auth/RegisterController
php artisan make:controller Auth/LoginController
php artisan make:controller Auth/EmailVerificationController
php artisan make:controller Auth/PhoneVerificationController
```

#### 2.2 Services
```bash
php artisan make:service EmailService
php artisan make:service SmsService
```

#### 2.3 Middleware
```bash
php artisan make:middleware EnsureEmailVerified
php artisan make:middleware EnsurePhoneVerified
```

---

### Phase 3: Core Functionality (День 5-7)

#### 3.1 Zakupki (Закупки)
```bash
php artisan make:controller ZakupkiController
php artisan make:model Zakupki
php artisan make:request ZakupkiFilterRequest
```

#### 3.2 Companies (Предприятия)
```bash
php artisan make:controller CompanyController
php artisan make:model Company
php artisan make:request CompanyFilterRequest
```

#### 3.3 Export
```bash
php artisan make:export ZakupkiExport
php artisan make:export CompaniesExport
```

---

### Phase 4: Payment Integration (День 8-9)

#### 4.1 YooKassa Integration
```bash
php artisan make:controller PaymentController
php artisan make:service PaymentService
```

#### 4.2 Balance Management
```bash
php artisan make:middleware CheckBalance
```

---

### Phase 5: Admin Panel (День 10-11)

#### 5.1 Admin Controllers
```bash
php artisan make:controller Admin/UserController
php artisan make:controller Admin/NewsController
php artisan make:controller Admin/IdeaController
php artisan make:controller Admin/SqlQueryController
```

#### 5.2 Admin Middleware
```bash
php artisan make:middleware IsAdmin
```

---

### Phase 6: UI & Views (День 12-14)

#### 6.1 Layouts
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/guest.blade.php`

#### 6.2 Pages
- Zakupki list & detail
- Companies list & detail
- User profile
- Admin panel

---

## 🛠 Команды для быстрого старта

### Создание всех миграций
```bash
docker exec devilbox-php83-1 bash -c "cd /shared/httpd/businessdb && \
php artisan make:migration add_role_balance_to_users_table && \
php artisan make:migration create_transactions_table && \
php artisan make:migration create_news_table && \
php artisan make:migration create_ideas_table && \
php artisan make:migration create_email_verifications_table"
```

### Создание всех моделей
```bash
docker exec devilbox-php83-1 bash -c "cd /shared/httpd/businessdb && \
php artisan make:model Transaction && \
php artisan make:model News && \
php artisan make:model Idea && \
php artisan make:model EmailVerification"
```

### Создание контроллеров
```bash
docker exec devilbox-php83-1 bash -c "cd /shared/httpd/businessdb && \
php artisan make:controller ZakupkiController && \
php artisan make:controller CompanyController && \
php artisan make:controller PaymentController"
```

---

## 📦 Необходимые пакеты (уже установлены)

- ✅ `laravel/breeze` - Authentication scaffolding
- ✅ `maatwebsite/excel` - Excel import/export
- ✅ `doctrine/dbal` - Database abstraction layer

### Дополнительные пакеты (установить по необходимости)

```bash
# SMS.ru API клиент
composer require avp/smsru

# YooKassa SDK
composer require yookassa/yookassa-sdk-php

# MSSQL драйвер (уже должен быть в PHP)
# php -m | grep sqlsrv
```

---

## 🗂 Структура проекта (Production-Ready)

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/
│   │   │   ├── RegisterController.php
│   │   │   ├── LoginController.php
│   │   │   └── VerificationController.php
│   │   ├── ZakupkiController.php
│   │   ├── CompanyController.php
│   │   └── PaymentController.php
│   ├── Middleware/
│   │   ├── CheckBalance.php
│   │   ├── IsAdmin.php
│   │   └── EnsureVerified.php
│   └── Requests/
│       ├── ZakupkiFilterRequest.php
│       └── CompanyFilterRequest.php
├── Models/
│   ├── User.php
│   ├── Transaction.php
│   ├── News.php
│   ├── Idea.php
│   └── EmailVerification.php
├── Services/
│   ├── EmailService.php
│   ├── SmsService.php
│   └── PaymentService.php
├── Exports/
│   ├── ZakupkiExport.php
│   └── CompaniesExport.php
└── Helpers/
    └── MaskingHelper.php

resources/
├── views/
│   ├── layouts/
│   │   ├── app.blade.php
│   │   └── guest.blade.php
│   ├── zakupki/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   ├── companies/
│   │   ├── index.blade.php
│   │   └── show.blade.php
│   └── admin/
│       ├── users.blade.php
│       ├── news.blade.php
│       └── ideas.blade.php
└── lang/
    └── ru/
        └── messages.php

database/
├── migrations/
│   ├── 2024_xx_xx_add_fields_to_users_table.php
│   ├── 2024_xx_xx_create_transactions_table.php
│   ├── 2024_xx_xx_create_news_table.php
│   ├── 2024_xx_xx_create_ideas_table.php
│   └── 2024_xx_xx_create_email_verifications_table.php
└── seeders/
    └── AdminUserSeeder.php

routes/
├── web.php
├── admin.php (optional)
└── api.php (future)

config/
├── database.php (MSSQL connection)
├── services.php (SMS, Email, Payment configs)
└── businessdb.php (custom config)
```

---

## 🔐 Security Best Practices

1. ✅ **CSRF Protection** - встроен в Laravel
2. ✅ **SQL Injection** - Eloquent ORM
3. ✅ **XSS Protection** - Blade escaping
4. ✅ **Password Hashing** - bcrypt/argon2
5. ✅ **Email Verification** - custom implementation
6. ✅ **Rate Limiting** - Laravel throttle
7. ✅ **Admin Middleware** - role-based access

---

## 🧪 Testing Strategy

```bash
# Unit Tests
php artisan make:test UserTest --unit
php artisan make:test TransactionTest --unit

# Feature Tests
php artisan make:test Auth/RegistrationTest
php artisan make:test ZakupkiTest
php artisan make:test CompanyTest

# Run tests
php artisan test
```

---

## 📝 Next Steps

1. ✅ Создать миграции для всех таблиц
2. ✅ Создать модели с relationships
3. ✅ Настроить MSSQL connection
4. ✅ Портировать authentication логику
5. ✅ Создать контроллеры для Zakupki/Companies
6. ✅ Портировать UI (Blade templates)
7. ✅ Интегрировать Payment System
8. ✅ Тестирование
9. ✅ Deployment

---

## 🚀 Deployment Checklist

- [ ] `.env` production settings
- [ ] Database migrations
- [ ] Composer optimize autoload
- [ ] Cache config, routes, views
- [ ] Queue workers setup
- [ ] Log rotation
- [ ] SSL certificate
- [ ] Backup strategy

---

**Статус:** Laravel успешно установлен и готов к миграции! 🎉
**Доступ:** https://businessdb.dvl.to/
**Следующий шаг:** Создание миграций и моделей
