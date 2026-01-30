<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\UserService;
use App\Services\BalanceService;
use App\Services\BetService;
use App\Services\EventService;

final class ClientController
{
    public function dashboard(): void
    {
        // Просто выводим HTML для клиента
        require __DIR__ . '/../views/client/dashboard.php';
    }





//    private UserService $userService;
//    private BalanceService $balanceService;
//    private BetService $betService;
//    private EventService $eventService;
//
//    public function __construct(
//        UserService $userService,
//        BalanceService $balanceService,
//        BetService $betService,
//        EventService $eventService
//    ) {
//        $this->userService = $userService;
//        $this->balanceService = $balanceService;
//        $this->betService = $betService;
//        $this->eventService = $eventService;
//    }

}
