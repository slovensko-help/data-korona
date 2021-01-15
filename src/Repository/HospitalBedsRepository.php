<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TimeSeries\HospitalBeds;
use App\Repository\Traits\Paginable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HospitalBedsRepository extends ServiceEntityRepository implements PaginableRepositoryInterface
{
    use Paginable;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HospitalBeds::class);
    }
}
