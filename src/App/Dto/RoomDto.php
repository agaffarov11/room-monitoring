<?php
declare(strict_types=1);

namespace App\Dto;

class RoomDto
{
    /**
     * @param null|InventoryLineDto[] $items
     */
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?int $capacity = null,
        public ?string $curator = null,
        public ?string $locatedAt = null,
        public ?array $items = null,
    ) {
    }

    public function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? null,
            $data['capacity'] ?? null,
            $data['curator'] ?? null,
            $data['locatedAt'] ?? null
        );
    }
}
