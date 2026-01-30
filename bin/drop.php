<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Database\Connection;

try {
    $pdo = Connection::getConnection();

    $sql = <<<SQL
DROP TABLE IF EXISTS
    bets,
    user_contacts,
    user_balances,
    events,
    users
CASCADE;
SQL;

    $pdo->exec($sql);

    echo "All tables dropped successfully\n";
} catch (\Throwable $e) {
    echo "Drop failed: " . $e->getMessage() . "\n";
    exit(1);
}
