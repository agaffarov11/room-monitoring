<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\ChangeTrackingPolicy;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[Entity]
#[Table(name: "tags")]
#[ChangeTrackingPolicy("DEFERRED_EXPLICIT")]
class Tag implements JsonSerializable
{
    #[Id]
    #[Column(type: UuidType::NAME)]
    private UuidInterface $id;

    #[Column(type: Types::STRING, unique: true)]
    private string $value;

    public function __construct(?UuidInterface $id, string $value)
    {
        $this->id    = $id ?? Uuid::uuid4();
        $this->value = $value;
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
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id, 'value' => $this->value];
    }
}
