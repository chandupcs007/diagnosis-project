$root = "C:\Laravel\phpmyadmin4.5"
$names = @(
    'string', 'binValue', 'sql_query', 'orientation', 'file', 'fitbox', 'dest',
    'font', 'line', 'element', 'dec', 'imglink', 'header', 'icc', 'color',
    'd_cur', 'arg_separator', 'version', 'attrib', 'trns', 'img'
)
$pattern = '(?<![\$\w])(' + ($names -join '|') + ')\['
$updated = 0

Get-ChildItem -Path $root -Filter *.php -Recurse | ForEach-Object {
    $path = $_.FullName
    $content = [System.IO.File]::ReadAllText($path)
    $new = [regex]::Replace($content, $pattern, {
        param($m)
        return '$' + $m.Groups[1].Value + '['
    })
    if ($new -ne $content) {
        [System.IO.File]::WriteAllText($path, $new)
        $updated++
    }
}

Write-Host "Restored dollar signs in $updated files."
