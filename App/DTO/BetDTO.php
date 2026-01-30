<?php
declare(strict_types=1);

namespace App\DTO;

final class BetDTO
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_WON = 'won';
    public const STATUS_LOST = 'lost';

    public ?int $id;
    public int $userId;
    public int $eventId;
    public string $outcome;
    public string $coefficient;
    public string $amount;
    public string $currency;
    public string $status;

    public function __construct(
        ?int $id,
        int $userId,
        int $eventId,
        string $outcome,
        string $coefficient,
        string $amount,
        string $currency,
        string $status = self::STATUS_PENDING
    ) {
        $this->id = $id;
        $this->userId = $userId;
        $this->eventId = $eventId;
        $this->outcome = $outcome;
        $this->coefficient = $coefficient;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->status = $status;
    }
}
