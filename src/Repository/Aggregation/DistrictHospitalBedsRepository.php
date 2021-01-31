<?php

declare(strict_types=1);

namespace App\Repository\Aggregation;

use App\Entity\Aggregation\DistrictHospitalBeds as Entity;
use App\Entity\TimeSeries\HospitalBeds;
use App\Entity\Traits\HospitalBedsData;
use Doctrine\Persistence\ManagerRegistry;

class DistrictHospitalBedsRepository extends AbstractDistrictRepository
{
    const SOURCE_TABLE_NAME = 'hospital_beds';
    const SOURCE_TRAIT_NAME = HospitalBedsData::class;
    const SOURCE_CLASS_NAME = HospitalBeds::class;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entity::class);
    }
}
