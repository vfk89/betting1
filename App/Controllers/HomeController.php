<?php
declare(strict_types=1);

namespace App\Controllers;

final class HomeController
{
    public function index(): void
    {
        require __DIR__ . '/../views/home/home.php';


    }
}
