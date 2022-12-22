<?php
declare(strict_types=1);

namespace App\Dto;

class InventoryLineDto
{
    public function __construct(
        public ?string $id = null,
        public ?string $name = null,
        public ?int $quantity = null,
    ) {
    }

    public function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['name'] ?? null,
            $data['quantity'] ?? null
        );
    }
}
