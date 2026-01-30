<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\ApiService;
use Throwable;

final readonly class ApiController
{
    public function __construct(private ApiService $apiService) {}

    public function listUsers(): void
    {
        $this->handle(fn() => $this->apiService->listUsers());
    }

    public function dashboard(int $userId): void
    {
        $this->handle(fn() => $this->apiService->dashboard($userId));
    }

    public function placeBet(array $input): void
    {
        $this->handle(fn() => $this->apiService->placeBet($input));
    }

    public function adminUpdateBalance(array $input): void
    {
        $this->handle(fn() => $this->apiService->adminUpdateBalance($input));
    }

    public function adminGetAllBets(): void
    {
        $this->handle(fn() => $this->apiService->adminGetAllBets());
    }

    public function adminSettleBet(array $input): void
    {
        $this->handle(fn() => $this->apiService->adminSettleBet($input));
    }

    public function adminSettleEvent(array $input): void
    {
        $this->handle(fn() => $this->apiService->adminSettleEvent($input));
    }

    /* ===== HELPERS ===== */

    private function handle(callable $fn): void
    {
        try {
            $result = $fn();
            $this->respond($result);
        } catch (Throwable $e) {
            $this->error($e->getMessage(), $e instanceof \RuntimeException ? 400 : 500);
        }
    }

    private function respond(array $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function error(string $message, int $status = 400): void
    {
        $this->respond(['success' => false, 'message' => $message], $status);
    }
}
