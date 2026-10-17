@echo off
cd /d "%~dp0"
echo START %DATE% %TIME% > tmp-build-status.txt
call npm ci > tmp-npmci.txt 2>&1
echo NPM_CI_EXIT=%ERRORLEVEL% >> tmp-build-status.txt
call npm run build > tmp-npmbuild.txt 2>&1
echo NPM_BUILD_EXIT=%ERRORLEVEL% >> tmp-build-status.txt
echo END %DATE% %TIME% >> tmp-build-status.txt
