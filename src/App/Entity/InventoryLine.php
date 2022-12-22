<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\ChangeTrackingPolicy;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[Entity]
#[Table(name: "inventory_lines")]
#[ChangeTrackingPolicy("DEFERRED_EXPLICIT")]
class InventoryLine
{
    #[Id]
    #[Column(type: UuidType::NAME)]
    private UuidInterface $id;

    #[Column(type: Types::STRING)]
    private string $name;

    #[Column(type: Types::INTEGER)]
    private int $quantity;

    public function __construct(string $name, int $quantity)
    {
        $this->id       = Uuid::uuid4();
        $this->name     = $name;
        $this->quantity = $quantity;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }
}
