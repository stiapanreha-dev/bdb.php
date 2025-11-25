@echo off
REM Export 10 records from zakupki to CSV file

set OUTPUT_FILE=zakupki_export.csv

echo Exporting data from zakupki to %OUTPUT_FILE%...
echo.

sqlcmd -S localhost -U sa -P 123123123 -d buss -s"," -W -Q "SET NOCOUNT ON; SELECT TOP 10 id, created, purchase_object, customer, start_cost FROM zakupki ORDER BY id DESC;" -o %OUTPUT_FILE%

if %ERRORLEVEL% EQU 0 (
    echo.
    echo Success! Data exported to %OUTPUT_FILE%
    echo.
    dir %OUTPUT_FILE%
) else (
    echo.
    echo Error during export!
)

echo.
pause
