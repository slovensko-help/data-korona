<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hospital;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Hospital|null find($id, $lockMode = null, $lockVersion = null)
 * @method Hospital|null findOneBy(array $criteria, array $orderBy = null)
 * @method Hospital[]    findAll()
 * @method Hospital[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HospitalRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Hospital::class);
    }

    public function findAllIndexedByCode() {
        $result = [];

        foreach ($this->findAll() as $hospital) {
            $result[$hospital->getCode()] = $hospital;
        }

        return $result;
    }
}
