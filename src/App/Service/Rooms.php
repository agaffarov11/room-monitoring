<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\InventoryLineDto;
use App\Dto\RoomDto;
use App\Entity\InventoryLine;
use App\Entity\Room;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Rooms
{
    private EntityManager $orm;
    private Buildings $buildingService;
    private Tags $tagService;

    public function __construct(EntityManager $orm, Buildings $buildingService, Tags $tagService)
    {
        $this->orm = $orm;
        $this->buildingService = $buildingService;
        $this->tagService = $tagService;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws EntityNotFoundException
     */
    public function create(RoomDto $dto): UuidInterface
    {
        $locatedAt = $this->buildingService->get($dto->locatedAt);
        $items     = array_map(
            fn(InventoryLineDto $item) => new InventoryLine($item->name, $item->quantity),
            $dto->items
        );

        $room = new Room(
            $dto->id,
            $dto->name,
            $dto->capacity,
            $dto->curator,
            $locatedAt,
            $items
        );

        $this->orm->persist($room);
        $this->orm->flush();

        return $room->getId();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws EntityNotFoundException
     */
    public function update(RoomDto $dto): void
    {
        $room = $this->get($dto->id);

        if ($dto->name) {
            $room->setName($dto->name);
        }

        if ($dto->capacity) {
            $room->setCapacity($dto->capacity);
        }

        if ($dto->curator) {
            $room->setCurator($dto->curator);
        }

        if ($dto->locatedAt) {
            $locatedAt = $this->buildingService->get($dto->locatedAt);

            $room->setLocatedAt($locatedAt);
        }

        $this->orm->persist($room);
        $this->orm->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws EntityNotFoundException
     */
    public function delete(UuidInterface | string $id): void
    {
        $room = $this->get($id);

        $this->orm->remove($room);
        $this->orm->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function addTag(UuidInterface | string $id, string $tag): void
    {
        $room = $this->get($id);
        $tag  = $this->tagService->get($tag);

        $room->addTag($tag);
        $this->orm->persist($room);
        $this->orm->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws EntityNotFoundException
     */
    public function addInventoryItem(UuidInterface | string $id, string $item, int $quantity): void
    {
        $room = $this->get($id);
        $item = new InventoryLine($item, $quantity);

        $room->addItemToInventory($item);
        $this->orm->persist($room);
        $this->orm->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function get(UuidInterface | string $id): ?Room
    {
        if (is_string($id) && !Uuid::isValid($id)) {
            throw new EntityNotFoundException();
        }

        if (!$id instanceof UuidInterface) {
            $id = Uuid::fromString($id);
        }

        $room = $this->find($id);

        if (!$room) {
            throw new EntityNotFoundException();
        }

        return $room;
    }

    public function find(UuidInterface $id): ?Room
    {
        return $this->orm->find(Room::class, $id);
    }
}
