<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\BuildingDto;
use App\Entity\Building;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Buildings
{
    private EntityManager $orm;
    private Tags $tagService;

    public function __construct(EntityManager $orm, Tags $tagService)
    {
        $this->orm        = $orm;
        $this->tagService = $tagService;
    }

    public function create(BuildingDto $dto): UuidInterface
    {
        $building = new Building($dto->id, $dto->name, $dto->capacity, $dto->curator);

        $this->orm->persist($building);
        $this->orm->flush();

        return $building->getId();
    }

    public function update(BuildingDto $dto): void
    {
        $building = $this->get(Uuid::fromString($dto->id));

        if ($dto->name) {
            $building->setName($dto->name);
        }

        if ($dto->capacity) {
            $building->setCapacity($dto->capacity);
        }

        if ($dto->curator) {
            $building->setCurator($dto->curator);
        }

        $this->orm->persist($building);
        $this->orm->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function delete(UuidInterface | string $id): void
    {
        $building = $this->get($id);

        $this->orm->remove($building);
        $this->orm->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function addTag(UuidInterface | string $id, string $tag): void
    {
        $building = $this->get($id);
        $tag      = $this->tagService->get($tag);

        $building->addTag($tag);
        $this->orm->persist($building);
        $this->orm->flush();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function get(UuidInterface | string $id): ?Building
    {
        if (is_string($id) && !Uuid::isValid($id)) {
            throw new EntityNotFoundException();
        }

        if (!$id instanceof UuidInterface) {
            $id = Uuid::fromString($id);
        }

        $building = $this->find($id);

        if (!$building) {
            throw new EntityNotFoundException();
        }

        return $building;
    }

    public function find(UuidInterface $id): ?Building
    {
        return $this->orm->find(Building::class, $id);
    }

    public function toBuildingDto(Building $building): BuildingDto
    {
        return new BuildingDto(
            (string) $building->getId(),
            $building->getName(),
            $building->getCapacity(),
            $building->getCurator()
        );
    }
}
