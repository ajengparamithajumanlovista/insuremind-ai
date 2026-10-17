@echo off
cd /d "%~dp0"
echo START %DATE% %TIME% > tmp-final-status.txt
set NODE_ENV=production
set CI=true
call npm ci > tmp-final-ci.txt 2>&1
echo CI_EXIT=%ERRORLEVEL% >> tmp-final-status.txt
call npm run build > tmp-final-build.txt 2>&1
echo BUILD_EXIT=%ERRORLEVEL% >> tmp-final-status.txt
echo END %DATE% %TIME% >> tmp-final-status.txt
