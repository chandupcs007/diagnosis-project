# Links C:\Laravel\phpmyadmin4.5 into diagnosis-project/public/phpmyadmin (junction).
# Run from an elevated PowerShell if junction creation fails.

$ProjectRoot = Split-Path -Parent $PSScriptRoot
$Target = "C:\Laravel\phpmyadmin4.5"
$Link = Join-Path $ProjectRoot "public\phpmyadmin"
$PmaConfig = Join-Path $Target "config.inc.php"
$LocalInclude = Join-Path $ProjectRoot "config\phpmyadmin.inc.php"
$IncludeSnippet = @"

/* diagnosis-project integration */
`$__diagnosisPmaConfig = '$($LocalInclude -replace "\\", "/")';
if (is_file(`$__diagnosisPmaConfig)) {
    include `$__diagnosisPmaConfig;
}
"@

if (-not (Test-Path $Target)) {
    Write-Error "phpMyAdmin folder not found: $Target"
    exit 1
}

if (Test-Path $Link) {
    Write-Host "Already linked: $Link"
} else {
    cmd /c mklink /J "$Link" "$Target"
    if ($LASTEXITCODE -ne 0) {
        Write-Error "Failed to create junction. Try running PowerShell as Administrator."
        exit 1
    }
    Write-Host "Created junction: $Link -> $Target"
}

if (Test-Path $PmaConfig) {
    $content = Get-Content $PmaConfig -Raw
    if ($content -notmatch 'diagnosis-project integration') {
        Add-Content -Path $PmaConfig -Value $IncludeSnippet
        Write-Host "Appended diagnosis-project include to config.inc.php"
    } else {
        Write-Host "config.inc.php already includes diagnosis-project settings"
    }
} else {
    Write-Warning "config.inc.php not found at $PmaConfig"
}

Write-Host ""
Write-Host "Set in .env:"
Write-Host "  PHPMYADMIN_ENABLED=true"
Write-Host "  PHPMYADMIN_URL=http://127.0.0.1:8000/phpmyadmin"
Write-Host ""
Write-Host "Start Laravel: php artisan serve"
Write-Host "Open: http://127.0.0.1:8000/phpmyadmin"
