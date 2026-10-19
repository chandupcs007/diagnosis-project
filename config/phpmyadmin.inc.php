<?php

/**
 * phpMyAdmin overrides for diagnosis-project (included from phpMyAdmin config.inc.php).
 * Reads DB_HOST / DB_PORT from the Laravel .env when present.
 */

$projectRoot = dirname(__DIR__);
$envValues = [];

$envFile = $projectRoot . DIRECTORY_SEPARATOR . '.env';
if (is_readable($envFile)) {
    foreach (file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = explode('=', $line, 2);
        $envValues[trim($key)] = trim($value, " \t\"'");
    }
}

$pmaBase = $envValues['PHPMYADMIN_URL']
    ?? $envValues['APP_URL'] ?? 'http://127.0.0.1:8000';
$pmaBase = rtrim($pmaBase, '/');
if (!str_ends_with(strtolower($pmaBase), '/phpmyadmin')) {
    $pmaBase .= '/phpmyadmin';
}

$cfg['PmaAbsoluteUri'] = $pmaBase . '/';

$i = 1;
$cfg['Servers'][$i]['host'] = $envValues['DB_HOST'] ?? '127.0.0.1';
$cfg['Servers'][$i]['port'] = $envValues['DB_PORT'] ?? '3306';
$cfg['Servers'][$i]['auth_type'] = 'cookie';
$cfg['Servers'][$i]['connect_type'] = 'tcp';
$cfg['Servers'][$i]['compress'] = false;
$cfg['Servers'][$i]['AllowNoPassword'] = false;
