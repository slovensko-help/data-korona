<?php

declare(strict_types=1);

namespace App\Repository\Raw;

use App\Entity\Raw\NcziMorningEmail as Entity;
use App\Repository\AbstractRepository;
use App\Repository\PaginableRepositoryInterface;
use App\Repository\Traits\Paginable;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entity[]    findAll()
 * @method Entity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NcziMorningEmailRepository extends AbstractRepository implements PaginableRepositoryInterface
{
    use Paginable;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entity::class);
    }
}
