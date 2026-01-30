<?php
use App\Controllers\ClientController;
use App\Controllers\AdminController;
use App\Controllers\ApiController;
use App\Services\ApiService;
use App\Controllers\HomeController;


$services = $services ?? require __DIR__ . '/bootstrap.php';

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// === Создаём сервисы и контроллеры один раз ===
$apiService = $services['apiService'];
$api = new ApiController($apiService);
$homeController = new HomeController();

$clientController = fn() => new ClientController(
    $services['userService'],
    $services['balanceService'],
    $services['betService'],
    $services['eventService']
);

$adminController = fn() => new AdminController(
    $services['userService'],
    $services['balanceService'],
    $services['betService'],
    $services['eventService']
);

// === Помощник для получения JSON из POST ===
$getInput = fn() => json_decode(file_get_contents('php://input'), true) ?? [];

// === Роуты ===
$routes = [
    // CLIENT
    ['GET', '/', fn() => $homeController->index()],
    ['GET', '/client', fn() => $clientController()->dashboard()],

    // ADMIN
    ['GET', '/admin', fn() => $adminController()->dashboard()],

    // API CLIENT
    ['GET', '/api/users', fn() => $api->listUsers()],
    ['GET', '/api/dashboard/{id}', fn($params) => $api->dashboard((int)$params['id'])],
    ['POST', '/api/bets', fn() => $api->placeBet($getInput())],

    // API ADMIN
    ['POST', '/api/admin/balance', fn() => $api->adminUpdateBalance($getInput())],
    ['GET', '/api/admin/bets', fn() => $api->adminGetAllBets()],
    ['POST', '/api/admin/bets/{betId}/settle', fn($params) => $api->adminSettleBet(
        array_merge($getInput(), ['betId' => (int)$params['betId']])
    )],
    ['POST', '/api/admin/events/{eventId}/settle', fn($params) => $api->adminSettleEvent(
        array_merge($getInput(), ['eventId' => (int)$params['eventId']])
    )],
];

// === Роутинг с параметрами ===
$found = false;

foreach ($routes as [$routeMethod, $routePath, $handler]) {
    if ($method !== $routeMethod) continue;

    $routeParts = explode('/', trim($routePath, '/'));
    $uriParts   = explode('/', trim($uri, '/'));

    if (count($routeParts) !== count($uriParts)) continue;

    $params = [];
    $matched = true;

    foreach ($routeParts as $i => $part) {
        if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
            $key = trim($part, '{}');
            $params[$key] = $uriParts[$i];
        } elseif ($part !== $uriParts[$i]) {
            $matched = false;
            break;
        }
    }

    if ($matched) {
        $handler($params);
        $found = true;
        break;
    }
}

if (!$found) {
    http_response_code(404);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['success' => false, 'message' => 'Page not found'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}
