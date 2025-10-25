# Настройка автоматического деплоя через GitHub Actions

## Что делает этот workflow?

При каждом push в ветку `main` автоматически:
1. Подключается к продакшн-серверу по SSH
2. Выполняет `git pull origin main`
3. Обновляет composer зависимости
4. Запускает миграции БД
5. Очищает кеши Laravel
6. Перезапускает PHP-FPM

## Настройка GitHub Secrets

Необходимо добавить следующие секреты в настройках GitHub репозитория:

### Как добавить секреты:
1. Откройте репозиторий: https://github.com/stiapanreha-dev/bdb.php
2. Перейдите в **Settings** → **Secrets and variables** → **Actions**
3. Нажмите **New repository secret**
4. Добавьте каждый из секретов ниже:

### Необходимые секреты:

#### 1. SSH_HOST
```
176.117.212.121
```

#### 2. SSH_USERNAME
```
lexun
```

#### 3. SSH_PORT
```
22
```

#### 4. SSH_PRIVATE_KEY
Содержимое приватного SSH ключа из `~/.ssh/id_smtb`

**Как получить:**
На вашем локальном компьютере выполните:
```bash
cat ~/.ssh/id_smtb
```

Скопируйте **весь** вывод (включая строки `-----BEGIN OPENSSH PRIVATE KEY-----` и `-----END OPENSSH PRIVATE KEY-----`) и вставьте как значение секрета.

## Настройка сервера

### 1. Убедитесь, что пользователь lexun может выполнять sudo без пароля для PHP-FPM:

На сервере выполните:
```bash
sudo visudo
```

Добавьте строку:
```
lexun ALL=(ALL) NOPASSWD: /bin/systemctl restart php8.3-fpm
```

### 2. Убедитесь, что composer установлен глобально:

```bash
which composer
# Должен вывести путь, например /usr/local/bin/composer
```

Если composer не установлен:
```bash
cd /home/alex/businessdb
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
```

### 3. Проверьте права на директорию проекта:

```bash
ls -la /home/alex/businessdb
# Владелец должен быть: lexun или alex
```

Если нужно изменить владельца:
```bash
sudo chown -R lexun:lexun /home/alex/businessdb
```

## Тестирование

После настройки секретов:
1. Сделайте любой коммит в ветку `main`
2. Откройте вкладку **Actions** в GitHub репозитории
3. Наблюдайте за выполнением workflow
4. Проверьте сайт https://businessdb.ru

## Откат в случае проблем

Если что-то пошло не так, подключитесь к серверу и откатите изменения вручную:

```bash
ssh -i ~/.ssh/id_smtb lexun@176.117.212.121
cd /home/alex/businessdb
git log --oneline -5  # Посмотреть последние коммиты
git reset --hard COMMIT_HASH  # Откатиться к нужному коммиту
sudo systemctl restart php8.3-fpm
```

## Отключение автодеплоя

Если нужно временно отключить автодеплой, удалите или переименуйте файл:
```bash
mv .github/workflows/deploy.yml .github/workflows/deploy.yml.disabled
```

## Логи деплоя

Все логи доступны в GitHub Actions:
https://github.com/stiapanreha-dev/bdb.php/actions

## Безопасность

- ✅ Приватный SSH ключ хранится в GitHub Secrets (зашифрован)
- ✅ Ключ никогда не показывается в логах
- ✅ Доступ к серверу только через SSH ключ
- ✅ Миграции выполняются с флагом `--force` (без интерактивного подтверждения)

## Что НЕ делается автоматически

- ❌ Установка новых системных пакетов (PHP расширения, PostgreSQL и т.д.)
- ❌ Изменения в конфигурации Nginx
- ❌ Обновление .env файла (нужно делать вручную)
- ❌ Создание бэкапов БД (настройте отдельно)

Для таких изменений подключайтесь к серверу вручную.
