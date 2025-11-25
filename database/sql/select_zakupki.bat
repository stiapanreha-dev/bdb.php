@echo off
REM Скрипт для выборки 10 записей из таблицы zakupki
REM Использование: select_zakupki.bat

echo Подключение к SQL Server и выборка данных из zakupki...
echo.

sqlcmd -S 172.26.192.1 -U sa -P 123123123 -d business2025 -Q "SET NOCOUNT ON; SELECT TOP 10 id, created, purchase_object, customer, start_cost FROM zakupki ORDER BY id DESC;"

echo.
echo Готово!
pause
