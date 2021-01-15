<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Aggregation\RegionHospitalBeds;
use App\Repository\Traits\Paginable;
use Doctrine\Persistence\ManagerRegistry;

class RegionHospitalBedsRepository extends AbstractRepository implements PaginableRepositoryInterface
{
    use Paginable;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RegionHospitalBeds::class);
    }
}
