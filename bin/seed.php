<?php
declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Database\Connection;
use App\DTO\UserDTO;
use App\DTO\BalanceDTO;
use App\DTO\ContactDTO;
use App\DTO\EventDTO;

$pdo = Connection::getConnection();

try {
    $pdo->beginTransaction();

    // =========================
    // Пользователи
    // =========================
    $users = [
        new UserDTO(
            null,
            'ivan_ivanov',
            password_hash('password123', PASSWORD_DEFAULT),
            'Иван Иванов',
            'male',
            '1991-03-15'
        ),
        new UserDTO(
            null,
            'petr_petrov',
            password_hash('secret456', PASSWORD_DEFAULT),
            'Пётр Петров',
            'male',
            '1988-11-02'
        ),
    ];

    $userIds = [];

    foreach ($users as $user) {
        $stmt = $pdo->prepare(
            'INSERT INTO users (login, password, name, gender, birth_date, status)
             VALUES (:login, :password, :name, :gender, :birth_date, :status)'
        );

        $stmt->execute([
            'login' => $user->login,
            'password' => $user->getHashedPassword(),
            'name' => $user->name,
            'gender' => $user->gender,
            'birth_date' => $user->birthDate,
            'status' => $user->status,
        ]);

        $userIds[] = (int)$pdo->lastInsertId();
    }

    // =========================
    // Контакты
    // =========================
    $contacts = [
        new ContactDTO($userIds[0], 'phone', '+79990001111'),
        new ContactDTO($userIds[0], 'phone', '+79990002222'),
        new ContactDTO($userIds[0], 'email', 'ivan.ivanov@gmail.com'),
        new ContactDTO($userIds[0], 'email', 'ivan.work@mail.ru'),

        new ContactDTO($userIds[1], 'phone', '+79991112233'),
        new ContactDTO($userIds[1], 'phone', '+79994445566'),
        new ContactDTO($userIds[1], 'email', 'petr.petrov@gmail.com'),
        new ContactDTO($userIds[1], 'email', 'petr.personal@mail.ru'),
    ];

    foreach ($contacts as $contact) {
        $stmt = $pdo->prepare(
            'INSERT INTO user_contacts (user_id, type, value)
             VALUES (:user_id, :type, :value)'
        );

        $stmt->execute([
            'user_id' => $contact->userId,
            'type' => $contact->type,
            'value' => $contact->value,
        ]);
    }

    // =========================
    // Балансы
    // =========================
    $balances = [
        new BalanceDTO($userIds[0], 'EUR', '30000.00'),
        new BalanceDTO($userIds[0], 'USD', '10000.00'),
        new BalanceDTO($userIds[1], 'EUR', '20000.00'),
        new BalanceDTO($userIds[1], 'USD', '15000.00'),
    ];

    foreach ($balances as $bal) {
        $stmt = $pdo->prepare(
            'INSERT INTO user_balances (user_id, currency, balance)
             VALUES (:user_id, :currency, :balance)'
        );

        $stmt->execute([
            'user_id' => $bal->userId,
            'currency' => $bal->currency,
            'balance' => $bal->balance,
        ]);
    }

    // =========================
    // События
    // =========================
    $events = [
        new EventDTO(null, 'Спартак vs Зенит'),
        new EventDTO(null, 'ЦСКА vs Динамо'),
        new EventDTO(null, 'Локомотив vs Рубин'),
    ];

    foreach ($events as $event) {
        $stmt = $pdo->prepare(
            'INSERT INTO events (title) VALUES (:title)'
        );
        $stmt->execute([
            'title' => $event->title,
        ]);
    }

    $pdo->commit();
    echo "✅ Seeding completed successfully\n";

} catch (\Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    echo "❌ Seeding failed: {$e->getMessage()}\n";
    exit(1);
}
