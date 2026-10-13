<?php

$env = [];
foreach (file(__DIR__.'/.env') as $line) {
    $line = trim($line);
    if ($line === '' || str_starts_with($line, '#') || ! str_contains($line, '=')) {
        continue;
    }
    [$k, $v] = explode('=', $line, 2);
    $env[trim($k)] = trim($v, " \t\"'");
}

echo 'DB_CONNECTION='.($env['DB_CONNECTION'] ?? '?').PHP_EOL;
echo 'DB_HOST='.($env['DB_HOST'] ?? '?').PHP_EOL;
echo 'DB_PORT='.($env['DB_PORT'] ?? '?').PHP_EOL;
echo 'DB_DATABASE='.($env['DB_DATABASE'] ?? '?').PHP_EOL;
echo 'DB_USERNAME='.($env['DB_USERNAME'] ?? '?').PHP_EOL;
echo 'SESSION_DRIVER='.($env['SESSION_DRIVER'] ?? '?').PHP_EOL;

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s',
        $env['DB_HOST'] ?? '127.0.0.1',
        $env['DB_PORT'] ?? '3306',
        $env['DB_DATABASE'] ?? ''
    );
    $pdo = new PDO($dsn, $env['DB_USERNAME'] ?? 'root', $env['DB_PASSWORD'] ?? '');
    echo 'MYSQL => OK'.PHP_EOL;

    $tables = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo 'TABLES('.count($tables).'): '.implode(', ', $tables).PHP_EOL;
} catch (Throwable $e) {
    echo 'MYSQL => FAIL: '.$e->getMessage().PHP_EOL;
}
