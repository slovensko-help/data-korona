<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hospital as Entity;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entity[]    findAll()
 * @method Entity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HospitalRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entity::class);
    }

    public function findAllIndexedByCode() {
        $result = [];

        foreach ($this->findAll() as $hospital) {
            $result[$hospital->getCode()] = $hospital;
        }

        return $result;
    }
}
