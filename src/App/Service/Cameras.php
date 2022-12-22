<?php
declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;

class Cameras
{
    private EntityManager $orm;

    public function __construct(EntityManager $orm)
    {
        $this->orm = $orm;
    }
}
