<?php

namespace App\Repository;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository as DefaultEntityRepository;
use Doctrine\ORM\Mapping;
use Symfony\Component\PropertyAccess\PropertyAccess;

class EntityRepository extends DefaultEntityRepository
{
    protected $propertyAccessor;
    protected $associationClassConfig = [];

    public function __construct(EntityManagerInterface $em, Mapping\ClassMetadata $class)
    {
        parent::__construct($em, $class);
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    public function findAllByKey(string $keyField, array $values)
    {
        $prefixedKeyField = 'o.' . $keyField;
        return $this->createQueryBuilder('o', $prefixedKeyField)
            ->andWhere($prefixedKeyField . ' IN (:values)')
            ->setParameter('values', $values)
            ->getQuery()
            ->getResult();
    }

    public function findAllByKeyForDeletion(string $keyField, array $excludedKeys, $minKey, $maxKey)
    {
        $prefixedKeyField = 'o.' . $keyField;
        $queryBuilder = $this->createQueryBuilder('o', $prefixedKeyField)
            ->andWhere($prefixedKeyField . ' NOT IN (:values)')
            ->setParameter('values', $excludedKeys);

        if (null !== $minKey) {
            $queryBuilder
                ->andWhere($prefixedKeyField . ' >= :minKey')
                ->setParameter('minKey', $minKey);
        }

        if (null !== $maxKey) {
            $queryBuilder
                ->andWhere($prefixedKeyField . ' < :maxKey')
                ->setParameter('maxKey', $maxKey);
        }

        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function findAllIndexedById()
    {
        $result = [];

        foreach ($this->findAll() as $entity) {
            $result[$entity->getId()] = $entity;
        }

        return $result;
    }

    public function findAllIndexedByCode()
    {
        $result = [];

        foreach ($this->findAll() as $entity) {
            $result[$entity->getCode()] = $entity;
        }

        return $result;
    }

    public function updateAllFromQuery(callable $queryBuilderCallback, callable $updateRecordCallback)
    {

        $query = $queryBuilderCallback($this)->execute();

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $entityClass = $this->getEntityName();

        $i = 1;

        while ($record = $query->fetch()) {
            $record = $updateRecordCallback($record);

            $entity = $this->findOneBy(['id' => $record['id']]) ?? new $entityClass();

            foreach ($record as $columnName => $columnValue) {
                $propertyAccessor->setValue($entity, $columnName, $columnValue);
            }

            $this->getEntityManager()->persist($entity);

            if ($i % 200 === 0) {
                yield true;
            }

            $i++;
        }

        yield true;
    }

    public function updateOrCreate(callable $updateEntityCallback, array $criteria, $flushAutomatically = false, $returnBeforeAndAfterUpdate = false)
    {
        $entity = $this->findOneBy($criteria);
        $beforeEntity = !$returnBeforeAndAfterUpdate || null === $entity ? null : clone $entity;
        $entityClass = $this->getClassName();

        $entity = $updateEntityCallback($entity ?? new $entityClass());

        if (null !== $entity) {
            $this->getEntityManager()->persist($entity);

            if ($flushAutomatically) {
                $this->getEntityManager()->flush();
            }
        }

        return $returnBeforeAndAfterUpdate ? [
            'before' => $beforeEntity,
            'after' => $entity,
        ] : $entity;
    }

    public function save(array $item, ...$relatedEntities)
    {
        throw new \Exception('Save method must be implemented in inherited class.');
    }

    /**
     * @param string $offsetId
     * @param DateTimeImmutable|null $updatedSince
     * @param int $limit
     * @return array
     */
    public function findOnePage(string $offsetId, ?DateTimeImmutable $updatedSince = null, ?DateTimeImmutable $publishedSince = null, int $limit = 1000): array
    {
        $qb = $this->createQueryBuilder('o')
            ->where('o.id < :offsetId')
            ->setParameter('offsetId', $offsetId)
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

    public function associationClasses(): array
    {
        return array_map(function (array $associationMapping) {
            return $associationMapping['targetEntity'];
        }, $this->getClassMetadata()->associationMappings);
    }

    protected function commitChangesToDb()
    {
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }
}
