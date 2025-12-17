@echo off
echo ========================================
echo   D'four Laundry - Starting Server
echo ========================================
echo.

REM Check if database exists
if not exist "database\laundry.db" (
    echo [INFO] Database not found. Initializing...
    php database\init.php
    echo.
)

REM Start Tailwind watch in background (optional)
REM start /B npm run dev

REM Start PHP server
echo [INFO] Starting PHP server on http://localhost:8000
echo [INFO] Press Ctrl+C to stop the server
echo.
php -S localhost:8000

pause
