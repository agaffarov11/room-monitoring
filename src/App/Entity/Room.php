<?php
declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\ChangeTrackingPolicy;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[Entity]
#[Table(name: "rooms")]
#[ChangeTrackingPolicy("DEFERRED_EXPLICIT")]
class Room implements JsonSerializable
{
    #[Id]
    #[Column(type: UuidType::NAME)]
    private UuidInterface $id;

    #[Column(type: Types::STRING)]
    private string $name;

    #[Column(type: Types::INTEGER)]
    private int $capacity;

    /** @var Collection<string, InventoryLine> */
    #[ManyToMany(
        targetEntity: InventoryLine::class,
        cascade: ["persist", "remove"],
        orphanRemoval: true,
        indexBy: "id"
    )]
    #[JoinTable(name: 'room_to_inventory_lines')]
    #[JoinColumn(name: 'room_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'inventory_line_id', referencedColumnName: 'id', unique: true)]
    private Collection $inventory;

    #[Column(type: Types::STRING)]
    private string $curator;

    #[ManyToOne(targetEntity: Building::class)]
    private Building $locatedAt;

    /** @var Collection<string, Camera> */
    #[ManyToMany(targetEntity: Camera::class, cascade: ["persist", "remove"], orphanRemoval: true, indexBy: "id")]
    #[JoinTable(name: 'room_to_cameras')]
    #[JoinColumn(name: 'room_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'camera_id', referencedColumnName: 'id', unique: true)]
    private Collection $cameras;

    /** @var Collection<string, Tag> */
    #[ManyToMany(targetEntity: Tag::class, cascade: ["persist"], orphanRemoval: true, indexBy: "id")]
    #[JoinTable(name: 'room_tags')]
    #[JoinColumn(name: 'room_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id')]
    private Collection $tags;

    public function __construct(
        ?UuidInterface $id,
        string         $name,
        int            $capacity,
        string         $curator,
        Building       $locatedAt,
        array          $inventory
    ) {
        $this->id        = $id ?? Uuid::uuid4();
        $this->name      = $name;
        $this->capacity  = $capacity;
        $this->curator   = $curator;
        $this->locatedAt = $locatedAt;
        $this->inventory = self::toItemsCollection($inventory);
        $this->cameras   = new ArrayCollection();
        $this->tags      = new ArrayCollection();
    }

    public function addCamera(Camera $camera): void
    {
        $this->cameras[(string) $camera->getId()] = $camera;
    }

    public function addItemToInventory(InventoryLine $inventoryLine): void
    {
        $this->inventory[(string) $inventoryLine->getId()] = $inventoryLine;
    }

    public function addTag(Tag $tag): void
    {
        $this->tags[(string) $tag->getId()] = $tag;
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
     * @return int
     */
    public function getCapacity(): int
    {
        return $this->capacity;
    }

    /**
     * @return string
     */
    public function getInventory(): string
    {
        return $this->inventory;
    }

    /**
     * @return string
     */
    public function getCurator(): string
    {
        return $this->curator;
    }

    /**
     * @return Building
     */
    public function getLocatedAt(): Building
    {
        return $this->locatedAt;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param int $capacity
     */
    public function setCapacity(int $capacity): void
    {
        $this->capacity = $capacity;
    }

    /**
     * @param string $inventory
     */
    public function setInventory(string $inventory): void
    {
        $this->inventory = $inventory;
    }

    /**
     * @param string $curator
     */
    public function setCurator(string $curator): void
    {
        $this->curator = $curator;
    }

    /**
     * @param Building $locatedAt
     */
    public function setLocatedAt(Building $locatedAt): void
    {
        $this->locatedAt = $locatedAt;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'inventory' => $this->inventory,
            'curator' => $this->curator,
            'locatedAt' => $this->locatedAt,
            'cameras' => $this->cameras->toArray(),
            'tags' => $this->tags->toArray()
        ];
    }

    private static function toItemsCollection(array $items): Collection
    {
        return new ArrayCollection(
            array_combine(
                array_map(fn(InventoryLine $item) => (string) $item->getId(), $items),
                array_values($items)
            )
        );
    }
}
