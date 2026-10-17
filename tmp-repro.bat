@echo off
cd /d "%~dp0"
echo START %DATE% %TIME% > tmp-build-status3.txt
set NODE_ENV=production
set CI=true
set NIXPACKS=true
node tmp-repro.cjs > tmp-repro.txt 2>&1
echo REPRO_EXIT=%ERRORLEVEL% >> tmp-build-status3.txt
echo END %DATE% %TIME% >> tmp-build-status3.txt
