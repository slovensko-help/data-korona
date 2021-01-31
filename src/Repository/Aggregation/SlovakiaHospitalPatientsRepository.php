<?php

declare(strict_types=1);

namespace App\Repository\Aggregation;

use App\Entity\Aggregation\SlovakiaHospitalPatients as Entity;
use App\Entity\TimeSeries\HospitalPatients;
use App\Entity\Traits\HospitalPatientsData;
use Doctrine\Persistence\ManagerRegistry;

class SlovakiaHospitalPatientsRepository extends AbstractRepository
{
    const SOURCE_TABLE_NAME = 'hospital_patients';
    const SOURCE_TRAIT_NAME = HospitalPatientsData::class;
    const SOURCE_CLASS_NAME = HospitalPatients::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entity::class);
    }
}
