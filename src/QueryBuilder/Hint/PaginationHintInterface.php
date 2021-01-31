<?php

namespace App\QueryBuilder\Hint;

use App\QueryBuilder\PowerBiQueryBuilder;
use Generator;

interface PaginationHintInterface
{
    public function queryBuildersGenerator(PowerBiQueryBuilder $queryBuilder): Generator;
}