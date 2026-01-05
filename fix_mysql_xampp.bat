@echo off
echo ========================================
echo MySQL XAMPP Fix Script
echo ========================================
echo.

:: Check if running as administrator
net session >nul 2>&1
if %errorLevel% neq 0 (
    echo ERROR: This script must be run as Administrator!
    echo Right-click and select "Run as administrator"
    pause
    exit /b 1
)

echo Step 1: Stopping MySQL service...
net stop mysql >nul 2>&1
taskkill /F /IM mysqld.exe >nul 2>&1
timeout /t 2 >nul

echo Step 2: Checking if port 3306 is in use...
netstat -ano | findstr :3306
if %errorLevel% equ 0 (
    echo WARNING: Port 3306 is in use!
    echo Finding process using port 3306...
    for /f "tokens=5" %%a in ('netstat -ano ^| findstr :3306') do (
        echo Killing process %%a...
        taskkill /F /PID %%a >nul 2>&1
    )
    timeout /t 2 >nul
)

echo Step 3: Checking XAMPP MySQL directory...
set XAMPP_PATH=D:\xampp\mysql
if not exist "%XAMPP_PATH%" (
    echo ERROR: XAMPP MySQL directory not found at %XAMPP_PATH%
    echo Please update XAMPP_PATH in this script to match your installation
    pause
    exit /b 1
)

echo Step 4: Backing up InnoDB log files...
cd /d "%XAMPP_PATH%\data"
if exist "ib_logfile0" (
    echo Backing up ib_logfile0...
    copy "ib_logfile0" "ib_logfile0.backup" >nul 2>&1
)
if exist "ib_logfile1" (
    echo Backing up ib_logfile1...
    copy "ib_logfile1" "ib_logfile1.backup" >nul 2>&1
)

echo Step 5: Removing old InnoDB log files (will be recreated)...
del /F /Q "ib_logfile0" >nul 2>&1
del /F /Q "ib_logfile1" >nul 2>&1

echo Step 6: Checking for corrupted ibdata1...
if exist "ibdata1" (
    echo WARNING: ibdata1 exists. If MySQL still fails, you may need to restore from backup.
    echo (This script will NOT delete ibdata1 as it contains your data)
)

echo.
echo ========================================
echo Fix steps completed!
echo ========================================
echo.
echo Next steps:
echo 1. Open XAMPP Control Panel
echo 2. Try starting MySQL again
echo 3. If it still fails, check the MySQL error log:
echo    %XAMPP_PATH%\data\*.err
echo.
echo If MySQL still won't start, you may need to:
echo - Check Windows Event Viewer for more details
echo - Verify disk space is available
echo - Check file permissions on %XAMPP_PATH%\data
echo.
pause

