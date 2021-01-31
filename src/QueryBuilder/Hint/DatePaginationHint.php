<?php

namespace App\QueryBuilder\Hint;

use App\QueryBuilder\PowerBiQueryBuilder;
use DateInterval;
use DateTimeImmutable;
use Generator;

class DatePaginationHint implements PaginationHintInterface
{
    private $entityName;
    private $propertyName;
    private $fromDate;
    private $pageSizeInDays;

    public function __construct(string $entityName, string $propertyName, DateTimeImmutable $fromDate, $pageSizeInDays = 30)
    {
        $this->entityName = $entityName;
        $this->propertyName = $propertyName;
        $this->fromDate = $fromDate;
        $this->pageSizeInDays = $pageSizeInDays;
    }

    public function queryBuildersGenerator(PowerBiQueryBuilder $queryBuilder): Generator
    {
        $dateFieldIndex = $queryBuilder->fieldIndex($this->entityName, $this->propertyName);

        $fromDate = $this->fromDate;
        $lastItem = false;

        while (true) {
            if (null === $fromDate) {
                yield null;
            }

            $toDate = $fromDate->add(new DateInterval('P' . ($this->pageSizeInDays - 1) . 'D'));

            $lastItem = yield $this->pageQueryBuilder($queryBuilder, $lastItem, $fromDate, $toDate);

            if (null !== $lastItem) {
                $fromDate = $this->newFromDate($lastItem, $toDate, $dateFieldIndex);
            }
        }
    }

    private function newFromDate($lastItem, DateTimeImmutable $toDate, int $dateFieldIndex)
    {
        $lastItemDate = (new DateTimeImmutable())->setTimestamp($lastItem[$dateFieldIndex] / 1000);

        if ($lastItemDate->format('Y-m-d') === $toDate->format('Y-m-d')) {
            usleep(500000 - rand(0, 25000));
            return $toDate->add(new DateInterval('P1D'));
        }

        return null;
    }

    private function pageQueryBuilder(PowerBiQueryBuilder $queryBuilder, $lastItem, DateTimeImmutable $fromDate, DateTimeImmutable $toDate)
    {
        $from = $fromDate->format('Y-m-d');
        $to = $toDate->format('Y-m-d');

        return false === $lastItem ? null : (clone $queryBuilder)
            ->andWhere($this->entityName, $this->propertyName, PowerBiQueryBuilder::COMPARISON_GREATER_THAN_OR_EQUAL, "datetime'{$from}T00:00:00'")
            ->andWhere($this->entityName, $this->propertyName, PowerBiQueryBuilder::COMPARISON_LESS_THAN_OR_EQUAL, "datetime'{$to}T00:00:00'")
            ->orderBy($this->entityName, $this->propertyName, PowerBiQueryBuilder::ORDER_ASC);
    }
}