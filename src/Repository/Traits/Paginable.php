<?php

declare(strict_types=1);

namespace App\Repository\Traits;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;

trait Paginable {
    /**
     * @param int $offsetId
     * @param DateTimeImmutable|null $updatedSince
     * @param int $limit
     * @return array
     */
    public function findOnePage(int $offsetId, ?DateTimeImmutable $updatedSince = null, ?DateTimeImmutable $publishedSince = null, int $limit = 1000): array
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.id < :offsetId')
            ->setParameter('offsetId', $offsetId, Types::INTEGER)
            ->orderBy('o.id', 'DESC')
            ->setMaxResults($limit);

        if ($updatedSince instanceof DateTimeImmutable) {
            $qb->andWhere('o.updatedAt >= :updatedSince')
                ->setParameter('updatedSince', $updatedSince, Types::DATETIME_IMMUTABLE);
        }

        if ($publishedSince instanceof DateTimeImmutable) {
            $qb->andWhere('o.publishedOn >= :publishedSince')
                ->setParameter('publishedSince', $publishedSince, Types::DATETIME_IMMUTABLE);
        }

        return $qb->getQuery()->getResult();
    }
}