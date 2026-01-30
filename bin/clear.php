<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Database\Connection;

try {
    $pdo = Connection::getConnection();

    $sql = <<<SQL
TRUNCATE TABLE
    bets,
    user_contacts,
    user_balances,
    events,
    users
RESTART IDENTITY
CASCADE;
SQL;

    $pdo->exec($sql);

    echo "Database cleared successfully\n";
} catch (\Throwable $e) {
    echo "Database clear failed: " . $e->getMessage() . "\n";
    exit(1);
}
