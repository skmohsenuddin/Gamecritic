@echo off
echo Starting GameCritic Full Project...

:: Start PHP Server in background
echo Starting PHP Server on http://localhost:8000...
echo NOTE: Make sure MySQL is started in your XAMPP Control Panel!
start "" "C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t public

:: Start Python Server
echo Starting Python Server on http://localhost:5000...
cd python_service
..\venv\Scripts\python.exe app.py

pause
