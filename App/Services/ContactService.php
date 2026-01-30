<?php
declare(strict_types=1);

namespace App\Services;

use App\DTO\ContactDTO;
use App\Models\Contact;
use App\Repositories\ContactRepository;

final readonly class ContactService
{
    public function __construct(
        private ContactRepository $contacts
    ) {}

    public function add(int $userId, string $type, string $value): Contact
    {
        $dto = new ContactDTO(
            userId: $userId,
            type: $type,
            value: $value
        );

        $this->contacts->create($dto);

        return new Contact($dto);
    }

    /**
     * @return Contact[]
     */
    public function getUserContacts(int $userId): array
    {
        $dtos = $this->contacts->findByUserId($userId);

        return array_map(
            fn (ContactDTO $dto) => new Contact($dto),
            $dtos
        );
    }
}
