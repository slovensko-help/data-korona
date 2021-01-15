<?php

declare(strict_types=1);

namespace App\Repository;

use DateTimeImmutable;

interface PaginableRepositoryInterface
{
    public function findOnePage(int $offsetId, ?DateTimeImmutable $updatedAfter = null, int $limit = 1000): array;
}