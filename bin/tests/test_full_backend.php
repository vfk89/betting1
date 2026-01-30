<?php
declare(strict_types=1);

/**
 * FULL BACKEND TEST
 * Проверка всего функционала:
 * 1. Пользователь: создание, получение с контактами
 * 2. Контакты: добавление нескольких контактов, получение всех контактов
 * 3. Баланс: получение, уменьшение, увеличение, поддержка нескольких валют
 * 4. События/ставки: вывод событий, размещение ставок, расчёт выигрыша/проигрыша
 * 5. Контроллеры: BetController (dashboard, placeBet, settleBet), AdminController (updateUserBalance)
 */

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../bootstrap.php';

use App\DTO\BalanceDTO;
use App\DTO\BetDTO;
use App\DTO\ContactDTO;
use App\DTO\EventDTO;
use App\DTO\UserDTO;
use App\Repositories\UserRepository;
use App\Repositories\ContactRepository;
use App\Repositories\BalanceRepository;
use App\Repositories\EventRepository;
use App\Repositories\BetRepository;

use App\Services\UserService;
use App\Services\BalanceService;
use App\Services\EventService;
use App\Services\BetService;

use App\Controllers\ApiController;

// ----------------------------
// Репозитории
// ----------------------------
$userRepo    = new UserRepository();
$contactRepo = new ContactRepository();
$balanceRepo = new BalanceRepository();
$eventRepo   = new EventRepository();
$betRepo     = new BetRepository();

// ----------------------------
// Сервисы
// ----------------------------
$userService    = new UserService($userRepo, $contactRepo);
$balanceService = new BalanceService($balanceRepo);
$eventService   = new EventService($eventRepo);
$betService     = new BetService($betRepo, $balanceRepo, $balanceService);

// ----------------------------
// Контроллеры
// ----------------------------
$betController = new BetController(
    $userService,
    $balanceService,
    $eventService,
    $betService
);

// ----------------------------
// 1. Пользователь
// ----------------------------
echo "================= 1. Пользователи =================\n";

// Генерируем уникальный логин, чтобы не было конфликта
$uniqueLogin = 'test_user_' . uniqid();
$userDto = new UserDTO(
    $uniqueLogin,
    'password123',
    'Тест Пользователь',
    'male',
    '1995-01-01'
);
$userId = $userService->createUser($userDto);
echo "Создан пользователь с ID: {$userId}\n";

// Получаем пользователя с контактами (пока контактов нет)
$userData = $userService->getUserById($userId);
echo "Пользователь:\n";
var_dump($userData['user']);
echo "Контакты пользователя:\n";
var_dump($userData['contacts']);

// ----------------------------
// 2. Контакты
// ----------------------------
echo "\n================= 2. Контакты =================\n";

$contacts = [
    new ContactDTO($userId, 'phone', '+79990003333'),
    new ContactDTO($userId, 'phone', '+79990004444'),
    new ContactDTO($userId, 'email', 'test.user@gmail.com'),
    new ContactDTO($userId, 'email', 'test.personal@mail.ru')
];

foreach ($contacts as $contact) {
    $contactId = $contactRepo->create($contact);
    echo "Создан контакт с ID: {$contactId}\n";
}

// Получаем все контакты пользователя
$userContacts = $contactRepo->getByUserId($userId);
echo "Все контакты пользователя {$userId}:\n";
foreach ($userContacts as $c) {
    echo $c->getType() . ": " . $c->getValue() . "\n";
}

// ----------------------------
// 3. Баланс
// ----------------------------
echo "\n================= 3. Баланс =================\n";

$balanceRepo->create(new BalanceDTO($userId, 'EUR', '200.00'));
$balanceRepo->create(new BalanceDTO($userId, 'USD', '150.00'));

echo "Баланс EUR: " . $balanceService->getBalance($userId, 'EUR') . "\n";
echo "Баланс USD: " . $balanceService->getBalance($userId, 'USD') . "\n";

// Уменьшаем и увеличиваем баланс
$balanceService->decreaseBalance($userId, 'EUR', '50.00');
echo "Баланс EUR после decrease 50: " . $balanceService->getBalance($userId, 'EUR') . "\n";

$balanceService->increaseBalance($userId, 'EUR', '25.00');
echo "Баланс EUR после increase 25: " . $balanceService->getBalance($userId, 'EUR') . "\n";

// ----------------------------
// 4. События / ставки
// ----------------------------
echo "\n================= 4. События / ставки =================\n";

// Создаём события
$event1 = new EventDTO('Спартак vs Зенит');
$event2 = new EventDTO('ЦСКА vs Динамо');

$eventRepo->create($event1);
$eventRepo->create($event2);

$events = $eventRepo->getAll();
echo "Список событий:\n";
foreach ($events as $e) {
    echo "- " . $e->getDTO()->title . "\n";
}

// Размещаем ставку
$betId = $betService->placeBet(
    new BetDTO($userId, $events[0]->getId(), 'home', '2.50', '50.00', 'pending'),
    'EUR'
);
echo "Ставка размещена, ID: {$betId}\n";

// Рассчитываем выигрыш
$betService->settleWin($betId, 'EUR');
echo "Ставка {$betId} выиграла!\n";

// Размещаем вторую ставку на проигрыш
$betId2 = $betService->placeBet(
    new BetDTO($userId, $events[1]->getId(), 'away', '1.50', '30.00', 'pending'),
    'EUR'
);
$betService->settleLose($betId2);
echo "Ставка {$betId2} проиграла.\n";

// ----------------------------
// 5. Контроллеры
// ----------------------------
echo "\n================= 5. Контроллеры =================\n";

// BetController: dashboard
$dashboard = $betController->showDashboard($userId);
echo "Dashboard для пользователя {$userId}:\n";
var_dump($dashboard);

// Можно протестировать placeBet через контроллер
$betId3 = $betController->placeBet($userId, $events[0]->getId(), 'draw', '20.00', 'EUR', 3.05);
echo "Ставка через контроллер, ID: {$betId3}\n";

// settleBet через контроллер
$betController->settleBet($betId3, 'won', 'EUR');
echo "Ставка {$betId3} через контроллер выиграла.\n";

echo "\n================= FULL BACKEND TEST COMPLETE =================\n";
