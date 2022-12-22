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
use Doctrine\ORM\Mapping\Table;
use JsonSerializable;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[Entity]
#[Table(name: "buildings")]
#[ChangeTrackingPolicy("DEFERRED_EXPLICIT")]
class Building implements JsonSerializable
{
    #[Id]
    #[Column(type: UuidType::NAME)]
    private UuidInterface $id;

    #[Column(type: Types::STRING, length: 50)]
    private string $name;

    #[Column(type: Types::INTEGER)]
    private int $capacity;

    #[Column(type: Types::STRING, length: 50)]
    private string $curator;

    /** @var Collection<string, Tag> */
    #[ManyToMany(targetEntity: Tag::class, cascade: ["persist"], orphanRemoval: true, indexBy: "id")]
    #[JoinTable(name: 'building_tags')]
    #[JoinColumn(name: 'building_id', referencedColumnName: 'id')]
    #[InverseJoinColumn(name: 'tag_id', referencedColumnName: 'id')]
    private Collection $tags;

    public function __construct(?UuidInterface $id, string $name, int $capacity, string $curator)
    {
        $this->id       = $id ?? Uuid::uuid4();
        $this->name     = $name;
        $this->capacity = $capacity;
        $this->curator  = $curator;
        $this->tags     = new ArrayCollection();
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
    public function getCurator(): string
    {
        return $this->curator;
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
     * @param string $curator
     */
    public function setCurator(string $curator): void
    {
        $this->curator = $curator;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'capacity' => $this->capacity,
            'curator' => $this->curator,
            'tags' => $this->tags->toArray()
        ];
    }
}
