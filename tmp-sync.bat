@echo off
cd /d "%~dp0"
echo START %DATE% %TIME% > tmp-sync-status.txt
call npm install > tmp-npminstall.txt 2>&1
echo NPM_INSTALL_EXIT=%ERRORLEVEL% >> tmp-sync-status.txt
call npm ci > tmp-npmci-final.txt 2>&1
echo NPM_CI_EXIT=%ERRORLEVEL% >> tmp-sync-status.txt
call npm run build > tmp-npmbuild-final.txt 2>&1
echo NPM_BUILD_EXIT=%ERRORLEVEL% >> tmp-sync-status.txt
node tmp-rollupinfo.cjs > tmp-rollupinfo2.txt 2>&1
node tmp-lockcheck.cjs > tmp-lockcheck2.txt 2>&1
echo END %DATE% %TIME% >> tmp-sync-status.txt
