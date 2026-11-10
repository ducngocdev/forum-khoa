@echo off
rem Chay file nay TRONG thu muc forum-khoa (hoac de setup.ps1 tu chep vao)
cd /d "%~dp0"
if not exist artisan (
  if exist "..\forum-khoa\artisan" ( cd /d "..\forum-khoa" ) else ( echo Khong tim thay du an forum-khoa & pause & exit /b 1 )
)
start "Vite" cmd /k npm run dev
rem Laravel file upload validation requires PHP's fileinfo extension.
start "Laravel" cmd /k "cd /d public && php -d extension=fileinfo -S 127.0.0.1:8000 ..\vendor\laravel\framework\src\Illuminate\Foundation\resources\server.php"
timeout /t 4 >nul
start http://127.0.0.1:8000
