<?php

use App\Repositories\ContactRepository;
use App\Repositories\UserRepository;
use App\Repositories\BalanceRepository;
use App\Repositories\EventRepository;
use App\Repositories\BetRepository;
use App\Services\ContactService;
use App\Services\UserService;
use App\Services\BalanceService;
use App\Services\EventService;
use App\Services\BetService;
use App\Services\ApiService;

// === Репозитории ===
$userRepo = new UserRepository();
$balanceRepo = new BalanceRepository();
$eventRepo = new EventRepository();
$betRepo = new BetRepository();
$contactRepo = new ContactRepository();

// === Сервисы ===
$userService = new UserService($userRepo);
$contactService = new ContactService($contactRepo);
$balanceService = new BalanceService($balanceRepo);
$eventService = new EventService($eventRepo);
$betService = new BetService($betRepo, $balanceService);

// === ApiService ===
$apiService = new ApiService(
    $userService,
    $balanceService,
    $betService,
    $eventService
);

// === Возвращаем массив для роутинга ===
return [
    'userService' => $userService,
    'balanceService' => $balanceService,
    'eventService' => $eventService,
    'betService' => $betService,
    'contactService' => $contactService,
    'apiService' => $apiService,
];
