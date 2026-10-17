@echo off
cd /d "%~dp0"
echo START %DATE% %TIME% > tmp-linux-status.txt
REM Simulate nixpacks/railpack Linux build machine (linux x64 glibc) on a Windows host:
REM npm will now report every optional dependency it cannot install.
set npm_config_os=linux
set npm_config_cpu=x64
set npm_config_libc=glibc
call npm ci --dry-run --include=dev > tmp-linux-dryrun.txt 2>&1
echo LINUX_DRYRUN_EXIT=%ERRORLEVEL% >> tmp-linux-status.txt
call npm ci --dry-run --include=dev > tmp-linux-ci.txt 2>&1
echo LINUX_CI_DRYRUN_EXIT=%ERRORLEVEL% >> tmp-linux-status.txt
set npm_config_platform=linux
call npm install --dry-run > tmp-linux-install.txt 2>&1
echo LINUX_INSTALL_DRYRUN_EXIT=%ERRORLEVEL% >> tmp-linux-status.txt
echo END %DATE% %TIME% >> tmp-linux-status.txt
