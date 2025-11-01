-- ПРОБЛЕМА: Запрос использует CONVERT(DATE, z.created) вместо z.created
-- Это означает что берутся ВСЕ закупки за сегодняшний день (с 00:00), а не с 17:30!

-- Query 1 (Keyword Set 1: программное, обеспечение, microsoft)
-- Период который ДОЛЖЕН БЫТЬ: 2025-11-01 17:30:00 to 2025-11-01 18:06:57
-- Период который ИСПОЛЬЗУЕТСЯ: 2025-11-01 00:00:00 to 2025-11-01 23:59:59
-- Результат: 163 записи (вместо ~8)

select top 1000 [z].[id], [z].[created] as [date_request], [z].[purchase_object], [z].[start_cost_var], [z].[start_cost], [z].[customer], ISNULL(z.email, z.additional_contacts) as email, [z].[contact_number] as [phone], [z].[post_address] as [address], [z].[purchase_type] from [zakupki] as [z] where CONVERT(DATE, z.created) >= '2025-11-01' and CONVERT(DATE, z.created) <= '2025-11-01' and ([z].[purchase_object] like '%программное%' or [z].[customer] like '%программное%' or [z].[purchase_object] like '%обеспечение%' or [z].[customer] like '%обеспечение%' or [z].[purchase_object] like '%microsoft%' or [z].[customer] like '%microsoft%') order by [z].[created] desc;

-- Query 2 (Keyword Set 2: компьютеры, ноутбуки)
-- Результат: 2 записи

select top 1000 [z].[id], [z].[created] as [date_request], [z].[purchase_object], [z].[start_cost_var], [z].[start_cost], [z].[customer], ISNULL(z.email, z.additional_contacts) as email, [z].[contact_number] as [phone], [z].[post_address] as [address], [z].[purchase_type] from [zakupki] as [z] where CONVERT(DATE, z.created) >= '2025-11-01' and CONVERT(DATE, z.created) <= '2025-11-01' and ([z].[purchase_object] like '%компьютеры%' or [z].[customer] like '%компьютеры%' or [z].[purchase_object] like '%ноутбуки%' or [z].[customer] like '%ноутбуки%') order by [z].[created] desc;

-- ИТОГО: 165 уникальных записей (163 + 2)
-- ПРАВИЛЬНО ДОЛЖНО БЫТЬ: ~8-10 записей (только с 17:30 до 18:06)

-- ИСПРАВЛЕНИЕ:
-- Заменить:
--   CONVERT(DATE, z.created) >= '2025-11-01'
--   CONVERT(DATE, z.created) <= '2025-11-01'
-- На:
--   z.created >= '2025-11-01 17:30:00'
--   z.created <= '2025-11-01 18:06:57'
