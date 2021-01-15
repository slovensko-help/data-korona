<?php

declare(strict_types=1);

namespace App\Repository\Traits;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;

trait Paginable {
    /**
     * @param int $offsetId
     * @param DateTimeImmutable|null $updatedAfter
     * @param int $limit
     * @return array
     */
    public function findOnePage(int $offsetId, ?DateTimeImmutable $updatedAfter = null, int $limit = 1000): array
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.id < :offsetId')
            ->setParameter('offsetId', $offsetId, Types::INTEGER)
            ->orderBy('o.id', 'DESC')
            ->setMaxResults($limit);

        if ($updatedAfter instanceof DateTimeImmutable) {
            $qb->andWhere('o.updatedAt > :updatedAfter')
                ->setParameter('updatedAfter', $updatedAfter, Types::DATETIME_IMMUTABLE);
        }

        return $qb->getQuery()->getResult();
    }
}