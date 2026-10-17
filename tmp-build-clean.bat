@echo off
cd /d "%~dp0"
echo START %DATE% %TIME% > tmp-build-status2.txt
call npm ci > tmp-npmci-clean.txt 2>&1
echo NPM_CI_EXIT=%ERRORLEVEL% >> tmp-build-status2.txt
call npm run build > tmp-npmbuild-clean.txt 2>&1
echo NPM_BUILD_EXIT=%ERRORLEVEL% >> tmp-build-status2.txt
echo END %DATE% %TIME% >> tmp-build-status2.txt
