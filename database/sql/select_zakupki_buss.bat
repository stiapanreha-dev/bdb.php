@echo off
REM Script to select 10 records from zakupki table (database: buss)

echo Connecting to SQL Server (database: buss)...
echo.

sqlcmd -S localhost -U sa -P 123123123 -d buss -Q "SET NOCOUNT ON; SELECT TOP 10 id, created, purchase_object, customer, start_cost FROM zakupki ORDER BY id DESC;"

echo.
echo Done!
pause
