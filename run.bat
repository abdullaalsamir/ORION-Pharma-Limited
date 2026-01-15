@echo off

start "Laravel Server" cmd /k ^
"cd /d C:\xampp\htdocs\orion-pharma-limited ^&^& call php artisan serve"

start "Vite Dev Server" cmd /k ^
"cd /d C:\xampp\htdocs\orion-pharma-limited ^&^& call npm run dev"

timeout /t 5 /nobreak >nul

start http://127.0.0.1:8000