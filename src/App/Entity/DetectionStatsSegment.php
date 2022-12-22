<?php
declare(strict_types=1);

namespace App\Entity;

use \DateTimeImmutable;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Ramsey\Uuid\Doctrine\UuidType;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[Entity]
class DetectionStatsSegment
{
    #[Id]
    #[Column(type: UuidType::NAME)]
    private UuidInterface $id;

    #[Column(type: Types::STRING)]
    private string $person;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[Column(type: Types::JSON)]
    private array $lastProbe;

    #[Column(type: Types::JSON)]
    private array $detections;

    #[Column(type: Types::FLOAT)]
    private float $average;

    public function __construct(?UuidInterface $id)
    {
        $this->id = $id ?? Uuid::uuid4();
    }

    public function addDetection(array $detection): void
    {
        $this->detections[] = $detection;
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
    public function getPerson(): string
    {
        return $this->person;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @return array
     */
    public function getLastProbe(): array
    {
        return $this->lastProbe;
    }

    /**
     * @return array
     */
    public function getProbes(): array
    {
        return $this->probes;
    }

    /**
     * @return float
     */
    public function getAverage(): float
    {
        return $this->average;
    }

    public function setLastProbe(array $lastProbe): void
    {
        $this->lastProbe = $lastProbe;
    }

    public function setAverage(float $average): void
    {
        $this->average = $average;
    }
}
