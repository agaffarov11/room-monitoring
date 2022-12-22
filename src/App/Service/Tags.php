<?php
declare(strict_types=1);

namespace App\Service;

use App\Dto\TagDto;
use App\Entity\Tag;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityNotFoundException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class Tags
{
    private EntityManager $orm;

    public function __construct(EntityManager $orm)
    {
        $this->orm = $orm;
    }

    public function create(TagDto $dto): UuidInterface
    {
        $tag = new Tag($dto->id, $dto->value);

        $this->orm->persist($tag);
        $this->orm->flush();

        return $tag->getId();
    }

    /**
     * @throws EntityNotFoundException
     */
    public function update(TagDto $dto): void
    {
        $tag = $this->get($dto->id);

        if ($dto->value) {
            $tag->setValue($dto->value);
        }

        $this->orm->persist($tag);
        $this->orm->flush();
    }

    /**
     * @throws OptimisticLockException
     * @throws EntityNotFoundException
     * @throws ORMException
     */
    public function delete(UuidInterface | string $id): void
    {
        $tag = $this->get($id);

        $this->orm->remove($tag);
        $this->orm->flush();
    }

    public function getTagByName(string $tagName): ?Tag
    {
        $query = $this->orm->createQuery("SELECT t FROM App\Entity\Tag t WHERE t.value=:tagName");

        $query->setParameter("tagName", $tagName);

        $result = $query->getResult();

        return $result[0];
    }

    /**
     * @throws EntityNotFoundException
     */
    public function get(UuidInterface | string $id): ?Tag
    {
        if (is_string($id) && !Uuid::isValid($id)) {
            throw new EntityNotFoundException();
        }

        if (!$id instanceof UuidInterface) {
            $id = Uuid::fromString($id);
        }

        $tag = $this->find($id);

        if (!$tag) {
            throw new EntityNotFoundException();
        }

        return $tag;
    }

    public function find(UuidInterface $id): ?Tag
    {
        return $this->orm->find(Tag::class, $id);
    }
}
