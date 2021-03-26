<?php

namespace App\QueryBuilder\Hint;

use App\QueryBuilder\PowerBiQueryBuilder;
use DateInterval;
use DateTimeImmutable;
use Generator;

class DatePaginationHint extends AbstractPaginationHint
{
    private $entityName;
    private $propertyName;
    private $fromDate;
    private $pageSizeInDays;
    private $pageSizeMultiplier = 1;
    private $today;

    public function __construct(string $entityName, string $propertyName, DateTimeImmutable $fromDate, $pageSizeInDays = 30)
    {
        $this->entityName = $entityName;
        $this->propertyName = $propertyName;
        $this->fromDate = $fromDate;
        $this->pageSizeInDays = $pageSizeInDays;
        $this->today = (new DateTimeImmutable())->setTime(0, 0);
    }

    public function pageQueryBuilders(PowerBiQueryBuilder $queryBuilder): Generator
    {
        while (null !== $this->fromDate) {
            // TODO: continue pagination when query returns more exactly MAX_PAGE_SIZE results (we don't know yet how to limit PowerBI Query to exact number of results)
            $toDate = $this->fromDate->add(new DateInterval('P' . min(static::MAX_PAGE_SIZE, $this->pageSizeInDays * $this->pageSizeMultiplier) . 'D'));

            // TODO: implement boundaries when $queryBuilder already has where condition with the same entityName.propertyName
            yield (clone clone $queryBuilder)
                ->andWhere($this->entityName, $this->propertyName, PowerBiQueryBuilder::COMPARISON_GREATER_THAN_OR_EQUAL, "datetime'{$this->fromDate->format('Y-m-d')}T00:00:00'")
                ->andWhere($this->entityName, $this->propertyName, PowerBiQueryBuilder::COMPARISON_LESS_THAN_OR_EQUAL, "datetime'{$toDate->format('Y-m-d')}T00:00:00'")
                ->orderBy($this->entityName, $this->propertyName, PowerBiQueryBuilder::ORDER_ASC);

            $this->fromDate = $this->newFromDate($toDate);
            $this->pageSizeMultiplier = null !== $this->pageHasItems && $this->pageHasItems ? 1 : 3; // let's widen the page for the next page to minimize request in case of large gaps
            $this->pageHasItems = false; // a caller which iterates over this generator must call PaginationHintInterface#pageHasItems() if queryBuilder yields any result
        }
    }

    private function newFromDate(DateTimeImmutable $toDate)
    {
        if ($toDate < $this->today) {
            return $toDate->add(new DateInterval('P1D'));
        }

        return null;
    }
}
