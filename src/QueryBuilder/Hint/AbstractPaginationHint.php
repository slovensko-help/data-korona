<?php

namespace App\QueryBuilder\Hint;

use App\QueryBuilder\PowerBiQueryBuilder;
use Generator;

abstract class AbstractPaginationHint implements PaginationHintInterface
{
    const MAX_PAGE_SIZE = 5000;

    protected $pageHasItems;

    public function pageHasItems()
    {
        $this->pageHasItems = false;
    }

    abstract public function pageQueryBuilders(PowerBiQueryBuilder $queryBuilder): Generator;
}