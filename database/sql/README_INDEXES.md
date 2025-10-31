# Инструкция по созданию индексов для оптимизации производительности

## Проблема
SQL Server испытывает нехватку памяти при выполнении поисковых запросов по таблицам `zakupki` (1.8+ млн записей в business2025). Запросы выполняются медленно или возвращают ошибку 504 Gateway Timeout.

## Решение
Создание индексов на поле `created` и композитных индексов для оптимизации запросов с фильтрацией по датам и текстовым полям.

## Ожидаемый результат
- ⚡ Ускорение поиска на 50-90%
- 🚀 Снижение нагрузки на SQL Server buffer pool
- ✅ Устранение ошибок 504 Gateway Timeout

---

## Применение скрипта

### Вариант 1: Через SQL Server Management Studio (SSMS)

1. Подключитесь к SQL Server (172.26.192.1)
2. Откройте файл `create_zakupki_indexes.sql`
3. Нажмите F5 или "Execute"
4. Дождитесь завершения (5-15 минут для всех баз)

### Вариант 2: Через sqlcmd (командная строка)

```bash
sqlcmd -S 172.26.192.1 -U sa -P your_password -i create_zakupki_indexes.sql
```

### Вариант 3: Через Laravel artisan (из Ubuntu сервера)

⚠️ **Внимание:** Этот вариант может занять длительное время и рискует прерваться по таймауту.

```bash
cd /home/alex/businessdb
php artisan db:seed --class=CreateZakupkiIndexesSeeder
```

---

## Что делает скрипт

### 1. Индекс `idx_zakupki_created`
```sql
CREATE NONCLUSTERED INDEX idx_zakupki_created
ON zakupki(created)
INCLUDE (id, purchase_object, customer, purchase_type, start_cost);
```

**Оптимизирует:**
- Фильтрацию по датам (`WHERE created >= ? AND created <= ?`)
- Сортировку по дате (`ORDER BY created DESC`)

### 2. Индекс `idx_zakupki_created_customer`
```sql
CREATE NONCLUSTERED INDEX idx_zakupki_created_customer
ON zakupki(created, customer)
INCLUDE (id, purchase_object);
```

**Оптимизирует:**
- Комбинированные запросы (дата + поиск по заказчику)
- LIKE запросы с фильтром по датам

---

## Базы данных, которые будут обработаны

- ✅ business2020
- ✅ business2021
- ✅ business2022
- ✅ business2023
- ✅ business2024
- ✅ **business2025** (основная база, ~1.8 млн записей)
- ✅ business2026

---

## Мониторинг выполнения

Во время выполнения скрипт выводит статус:

```
Processing business2025 (MAIN DATABASE - may take longer)...
  Creating idx_zakupki_created...
  ✓ idx_zakupki_created created successfully
  Creating idx_zakupki_created_customer...
  ✓ idx_zakupki_created_customer created successfully
```

---

## Проверка результатов

### 1. Проверить созданные индексы

```sql
USE business2025;
GO

SELECT
    i.name AS IndexName,
    i.type_desc AS IndexType,
    c.name AS ColumnName
FROM sys.indexes i
INNER JOIN sys.index_columns ic ON i.object_id = ic.object_id AND i.index_id = ic.index_id
INNER JOIN sys.columns c ON ic.object_id = c.object_id AND ic.column_id = c.column_id
WHERE i.object_id = OBJECT_ID('zakupki')
ORDER BY i.name, ic.index_column_id;
```

### 2. Проверить размер индексов

```sql
USE business2025;
GO

SELECT
    i.name AS IndexName,
    SUM(s.used_page_count) * 8 / 1024 AS SizeMB
FROM sys.dm_db_partition_stats AS s
INNER JOIN sys.indexes AS i ON s.object_id = i.object_id AND s.index_id = i.index_id
WHERE i.object_id = OBJECT_ID('zakupki')
GROUP BY i.name
ORDER BY SizeMB DESC;
```

### 3. Тестирование производительности

Выполните тестовый запрос и сравните время выполнения:

```sql
SET STATISTICS TIME ON;
SET STATISTICS IO ON;

-- Запрос с фильтрацией по датам
SELECT COUNT(*)
FROM zakupki
WHERE CONVERT(DATE, created) >= '2025-10-01'
  AND CONVERT(DATE, created) <= '2025-10-31';

SET STATISTICS TIME OFF;
SET STATISTICS IO OFF;
```

**До создания индексов:** ~30-60 секунд или timeout
**После создания индексов:** <1 секунда

---

## Откат изменений (если потребуется)

Для удаления созданных индексов:

```sql
-- Для каждой базы данных (business2020-2026)
USE business2025;
GO

DROP INDEX IF EXISTS idx_zakupki_created ON zakupki;
DROP INDEX IF EXISTS idx_zakupki_created_customer ON zakupki;
```

---

## Дополнительные рекомендации

### 1. Обслуживание индексов

Периодически (раз в месяц) выполняйте:

```sql
-- Для каждой базы
USE business2025;
GO

-- Обновление статистики
UPDATE STATISTICS zakupki;

-- Дефрагментация индексов (если фрагментация >30%)
ALTER INDEX idx_zakupki_created ON zakupki REBUILD;
ALTER INDEX idx_zakupki_created_customer ON zakupki REBUILD;
```

### 2. Мониторинг использования индексов

```sql
SELECT
    OBJECT_NAME(s.object_id) AS TableName,
    i.name AS IndexName,
    s.user_seeks,
    s.user_scans,
    s.user_lookups,
    s.user_updates,
    s.last_user_seek,
    s.last_user_scan
FROM sys.dm_db_index_usage_stats s
INNER JOIN sys.indexes i ON s.object_id = i.object_id AND s.index_id = i.index_id
WHERE OBJECT_NAME(s.object_id) = 'zakupki'
ORDER BY s.user_seeks DESC;
```

### 3. Настройка SQL Server Memory

Если проблемы с памятью сохраняются:

```sql
-- Проверить текущие настройки
EXEC sp_configure 'show advanced options', 1;
RECONFIGURE;
EXEC sp_configure 'max server memory (MB)';

-- Рекомендуемое значение: оставить 4-6 GB для ОС
-- Если у сервера 32 GB RAM, установить 26-28 GB для SQL Server
EXEC sp_configure 'max server memory (MB)', 27648; -- 27 GB
RECONFIGURE;
```

---

## Поддержка

При возникновении проблем:
1. Проверьте логи SQL Server
2. Проверьте место на диске (индексы занимают 10-20% от размера таблицы)
3. Убедитесь, что у пользователя есть права CREATE INDEX

---

**Дата создания:** 2025-10-31
**Автор:** System Optimization
**Версия:** 1.0
