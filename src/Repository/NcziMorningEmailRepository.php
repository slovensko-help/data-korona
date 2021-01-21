<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\NcziMorningEmail;
use App\Repository\Traits\Paginable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NcziMorningEmail|null find($id, $lockMode = null, $lockVersion = null)
 * @method NcziMorningEmail|null findOneBy(array $criteria, array $orderBy = null)
 * @method NcziMorningEmail[]    findAll()
 * @method NcziMorningEmail[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NcziMorningEmailRepository extends ServiceEntityRepository implements PaginableRepositoryInterface
{
    use Paginable;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NcziMorningEmail::class);
    }
}
