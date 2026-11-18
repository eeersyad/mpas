@echo off
title MPAS - Pelancar
rem Jalankan Reverb, Queue dan Serve sekaligus dalam SATU tetingkap Windows Terminal (3 tab)
cd /d "%~dp0"
set "PROJEK=%~dp0."

echo Membersihkan cache...
call php artisan optimize:clear >nul

where wt >nul 2>nul
if %errorlevel%==0 (
    wt -d "%PROJEK%" --title "Reverb" cmd /k php artisan reverb:start ; new-tab -d "%PROJEK%" --title "Queue" cmd /k php artisan queue:work --tries=3 ; new-tab -d "%PROJEK%" --title "Serve" cmd /k php artisan serve ; new-tab -d "%PROJEK%" --title "Scheduler" cmd /k php artisan schedule:work
) else (
    echo Windows Terminal tidak dijumpai, buka 3 tetingkap biasa...
    start "MPAS - Reverb" cmd /k php artisan reverb:start
    start "MPAS - Queue" cmd /k php artisan queue:work --tries=3
    start "MPAS - Serve" cmd /k php artisan serve
    start "MPAS - Scheduler" cmd /k php artisan schedule:work
)

echo MPAS sedang berjalan: Reverb, Queue, Serve dan Scheduler.
timeout /t 3 >nul
