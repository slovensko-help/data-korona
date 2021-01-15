<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Aggregation\DistrictHospitalBeds;
use App\Repository\Traits\Paginable;
use Doctrine\Persistence\ManagerRegistry;

class DistrictHospitalBedsRepository extends AbstractRepository implements PaginableRepositoryInterface
{
    use Paginable;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DistrictHospitalBeds::class);
    }
}
