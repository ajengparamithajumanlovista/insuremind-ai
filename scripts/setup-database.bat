@echo off
REM Pembungkus Windows untuk skrip penyiapan database berbasis SQLite.
REM Tidak memerlukan Laragon/MySQL. Cukup klik ganda atau jalankan:
REM   scripts\setup-database.bat

powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0setup-database.ps1"
pause
