# Use the project-local PHP runtime so Artisan and Composer use the same version.
$ErrorActionPreference = 'Stop'
$projectDirectory = Split-Path -Parent $PSScriptRoot
$runtimeExecutable = Join-Path $projectDirectory '.runtime/php84/php.exe'
$runtimeConfiguration = Join-Path $projectDirectory '.runtime/php84/php.ini'

if (-not (Test-Path -LiteralPath $runtimeExecutable)) {
    throw 'PHP lokal belum tersedia. Gunakan PHP 8.4 dengan ekstensi intl, atau siapkan .runtime/php84 sesuai README.'
}

Push-Location -LiteralPath $projectDirectory
try {
    & $runtimeExecutable -c $runtimeConfiguration @args
    $runtimeExitCode = $LASTEXITCODE
} finally {
    Pop-Location
}
exit $runtimeExitCode
