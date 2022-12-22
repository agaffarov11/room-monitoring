<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\DetectionStatsSegment;
use Doctrine\ORM\EntityManager;

class DetectionStatsSegmentService
{
    private EntityManager $orm;

    public function __construct(EntityManager $orm)
    {
        $this->orm = $orm;
    }

    public function makeProbe(int $count): void
    {
        $now   = (new \DateTimeImmutable())->modify("-5 minutes");
        $builder = $this->orm->createQueryBuilder();
        $builder
            ->select('d')
            ->from("DetectionStatsSegment", "d")
            ->where($builder->expr()->lt("d.createdAt", ":now"))
            ->orderBy("d.createdAt", "DESC")
            ->setMaxResults(1)
            ->setParameter("now", $now);

        $dss = $builder->getQuery()->getSingleResult();

        if ($dss) {
            $detection = ['time' => new \DateTimeImmutable(), 'count' => $count];

            $dss->addDetection($detection);
            $dss->setLastProbe($detection);

            $dss->setAverage();
        } else {

        }
    }
}
