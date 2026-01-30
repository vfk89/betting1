<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

define('BASE_PATH', dirname(__DIR__));

use App\Database\Connection;

$sqlFile = BASE_PATH . '/sql/schema.sql';

if (!file_exists($sqlFile)) {
    throw new RuntimeException('schema.sql not found');
}

try {
    $pdo = Connection::getConnection();

    $sql = file_get_contents($sqlFile);
    $pdo->exec($sql);

    echo "Migration completed successfully\n";
} catch (\Throwable $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
