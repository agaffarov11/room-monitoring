<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use JsonSerializable;
use Doctrine\ORM\Mapping\Id;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[Entity]
class Camera implements JsonSerializable
{
    #[Id]
    #[Column(type: UuidType::NAME)]
    private UuidInterface $id;

    public function __construct(?UuidInterface $id)
    {
        $this->id = $id ?? Uuid::uuid4();
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function jsonSerialize(): array
    {
        return ['id' => $this->id];
    }
}
