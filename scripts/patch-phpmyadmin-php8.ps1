$root = "C:\Laravel\phpmyadmin4.5"
$pattern = '(?<!\$)\$([a-zA-Z_][a-zA-Z0-9_]*(?:->[a-zA-Z_][a-zA-Z0-9_]*)*(?:\[[^\]]+\])?)\{([^}]+)\}'
$updated = 0

Get-ChildItem -Path $root -Filter *.php -Recurse | ForEach-Object {
    $path = $_.FullName
    $content = [System.IO.File]::ReadAllText($path)
    $new = [regex]::Replace($content, $pattern, {
        param($m)
        return '$' + $m.Groups[1].Value + '[' + $m.Groups[2].Value + ']'
    })
    if ($new -ne $content) {
        [System.IO.File]::WriteAllText($path, $new)
        $updated++
    }
}

Write-Host "Updated $updated PHP files for PHP 8 offset syntax."
