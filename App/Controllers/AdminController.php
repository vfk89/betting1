<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\UserService;
use App\Services\BalanceService;
use App\Services\BetService;
use App\Services\EventService;

final class AdminController
{
    public function dashboard(): void
    {
        // HTML админки
        require __DIR__ . '/../views/admin/dashboard.php';
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
