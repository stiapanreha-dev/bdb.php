@echo off
chcp 65001 >nul
REM Поиск SQL Server в сети

echo Поиск SQL Server...
echo.

echo 1. IP адрес localhost:
ping localhost -n 1
echo.

echo 2. Попытка подключения к localhost:
sqlcmd -S localhost -U sa -P 123123123 -Q "SELECT @@SERVERNAME AS ServerName, @@VERSION AS Version;" 2>nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] Подключение к localhost успешно!
    goto :found
)
echo.

echo 3. Попытка подключения к 127.0.0.1:
sqlcmd -S 127.0.0.1 -U sa -P 123123123 -Q "SELECT @@SERVERNAME AS ServerName;" 2>nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] Подключение к 127.0.0.1 успешно!
    goto :found
)
echo.

echo 4. Попытка подключения к localhost\SQLEXPRESS:
sqlcmd -S localhost\SQLEXPRESS -U sa -P 123123123 -Q "SELECT @@SERVERNAME AS ServerName;" 2>nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] Подключение к localhost\SQLEXPRESS успешно!
    goto :found
)
echo.

echo 5. Попытка подключения к 172.26.192.1:
sqlcmd -S 172.26.192.1 -U sa -P 123123123 -Q "SELECT @@SERVERNAME AS ServerName;" 2>nul
if %ERRORLEVEL% EQU 0 (
    echo [OK] Подключение к 172.26.192.1 успешно!
    goto :found
)
echo.

echo [ERROR] Не удалось подключиться ни к одному из адресов
echo.
echo Попробуйте проверить:
echo - Запущен ли SQL Server (services.msc)
echo - Включен ли SQL Server Authentication
echo - Правильный ли пароль для пользователя sa
goto :end

:found
echo.
echo Список баз данных:
sqlcmd -S %ERRORLEVEL% -U sa -P 123123123 -Q "SELECT name FROM sys.databases ORDER BY name;"

:end
echo.
pause
