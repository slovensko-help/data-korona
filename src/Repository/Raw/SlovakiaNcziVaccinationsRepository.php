<?php

declare(strict_types=1);

namespace App\Repository\Raw;

use App\Entity\Raw\SlovakiaNcziVaccinations as Entity;
use App\Repository\AbstractRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entity[]    findAll()
 * @method Entity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SlovakiaNcziVaccinationsRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entity::class);
    }

    public function save(array $item, ...$relatedEntities): Entity
    {
        $id = (int)$item['published_on']->format('Ymd');

        return $this->updateOrCreate(function (Entity $entity) use ($item, $id) {
            return $entity
                    ->setId($id)
                    ->setPublishedOn($item['published_on'])
                    ->setDose1Count($item['dose_1_count'])
                    ->setDose2Count($item['dose_2_count']);
        }, ['id' => $id]);
    }
}
