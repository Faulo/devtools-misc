setlocal
FOR /F "tokens=*" %%i in ('type .env') do SET %%i
findstr /C:"127.0.0.1 %STACK_NAME%" C:\Windows\System32\drivers\etc\hosts >nul 2>&1
if %ERRORLEVEL% NEQ 0 (
    echo 127.0.0.1 %STACK_NAME% >> C:\Windows\System32\drivers\etc\hosts
)
call docker stack deploy %STACK_NAME% --detach=true --prune --resolve-image=always -c=docker-compose.yml
endlocal