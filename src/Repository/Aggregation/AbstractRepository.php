<?php

declare(strict_types=1);

namespace App\Repository\Aggregation;

use App\Repository\ServiceEntityRepository;
use App\Tool\Id;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Generator;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use App\Tool\DateTime;

abstract class AbstractRepository extends ServiceEntityRepository
{
    const SOURCE_TABLE_NAME = '';
    const SOURCE_TRAIT_NAME = '';
    const SOURCE_CLASS_NAME = '';

    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    public function queryBuilder(): QueryBuilder
    {
        $aggregates = $this->aggregatableColumns(static::SOURCE_TRAIT_NAME, static::SOURCE_CLASS_NAME);

        $queryBuilder = $this->getEntityManager()->getConnection()->createQueryBuilder()
            ->select(
                'data.published_on',
                'MIN(data.reported_at) AS oldest_reported_at',
                'MAX(data.reported_at) AS newest_reported_at'
            )
            ->from(static::SOURCE_TABLE_NAME, 'data')
            ->groupBy('published_on');

        foreach ($aggregates as $aggregate) {
            $queryBuilder->addSelect("SUM($aggregate) AS $aggregate");
        };

        return $queryBuilder;
    }

    public function items(): Generator
    {
        $this->reloadDependantEntities();

        $query = $this->queryBuilder()->execute();

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $entityClass = $this->getEntityName();

        while ($item = $query->fetch()) {
            $item = $this->hydrateItem($item);

            $entity = $this->findOneBy(['id' => $item['id']]) ?? new $entityClass();

            foreach ($item as $columnName => $columnValue) {
                $propertyAccessor->setValue($entity, $columnName, $columnValue);
            }

            yield $entity;
        }
    }

    public function commitChangesToDb()
    {
        parent::commitChangesToDb();
        $this->reloadDependantEntities();
    }

    protected function hydrateItem(array $item): array
    {
        $item['published_on'] = DateTime::dateTimeFromString($item['published_on'], 'Y-m-d', true);;
        $item['id'] = Id::fromDateTimeAndInt($item['published_on'], 0);
        $item['oldest_reported_at'] = DateTime::dateTimeFromString($item['oldest_reported_at'], 'Y-m-d H:i:s');
        $item['newest_reported_at'] = DateTime::dateTimeFromString($item['newest_reported_at'], 'Y-m-d H:i:s');

        return $item;
    }

    protected function reloadDependantEntities()
    {
        // intentionally black
    }

    protected function withHospitalAndCity(QueryBuilder $queryBuilder): QueryBuilder
    {
        return $queryBuilder
            ->innerJoin('data', 'hospital', 'h', 'data.hospital_id = h.id')
            ->innerJoin('h', 'city', 'c', 'h.city_id = c.id');
    }

    private function aggregatableColumns(string $trait, string $entityClass): array
    {
        $traitProperties = (new PropertyInfoExtractor([new ReflectionExtractor()]))->getProperties($trait);
        $classMetadata = $this->getEntityManager()->getClassMetadata($entityClass);

        $fieldNameIndices = array_flip($classMetadata->getFieldNames());
        $columnNames = $classMetadata->getColumnNames();

        $result = [];

        foreach ($traitProperties as $traitPropertyName) {
            $result[$traitPropertyName] = $columnNames[$fieldNameIndices[$traitPropertyName]];
        }

        return $result;
    }
}
