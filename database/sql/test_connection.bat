@echo off
chcp 65001 >nul
REM Тест подключения к SQL Server

echo Тестирование подключения к SQL Server...
echo.

echo 1. Проверка подключения без указания базы данных:
sqlcmd -S 172.26.192.1 -U sa -P 123123123 -Q "SELECT @@VERSION;"
echo.

echo 2. Список доступных баз данных:
sqlcmd -S 172.26.192.1 -U sa -P 123123123 -Q "SELECT name FROM sys.databases ORDER BY name;"
echo.

pause
