<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TimeSeries\HospitalPatients;
use App\Repository\Traits\Paginable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class HospitalPatientsRepository extends ServiceEntityRepository implements PaginableRepositoryInterface
{
    use Paginable;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HospitalPatients::class);
    }
}
