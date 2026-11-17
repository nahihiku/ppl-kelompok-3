param([ValidateRange(1, 65535)][int]$Port = 8000)

# Preserve the Windows process environment when launching PHP's child server.
& "$PSScriptRoot/php.ps1" artisan serve --no-reload --host=127.0.0.1 "--port=$Port"
exit $LASTEXITCODE
