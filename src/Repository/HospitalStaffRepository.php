<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\TimeSeries\HospitalStaff;
use App\Repository\Traits\Paginable;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method HospitalStaff|null find($id, $lockMode = null, $lockVersion = null)
 * @method HospitalStaff|null findOneBy(array $criteria, array $orderBy = null)
 * @method HospitalStaff[]    findAll()
 * @method HospitalStaff[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HospitalStaffRepository extends AbstractRepository implements PaginableRepositoryInterface
{
    use Paginable;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, HospitalStaff::class);
    }
}
