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

//    public function keyValue(?callable $updater, string $keyColumn, ?array &$previousEntityClasses, ?array &$syncedEntities, ?array &$cachedEntities = null)
//    {
//        if (null === $updater) {
//            return null;
//        }
//
//        $entityClass = $this->getEntityName();
//
//        if (!isset($cachedEntities[$entityClass])) {
//            $cachedEntities[$entityClass] = [];
//        }
//
//        $classMateEntities = $cachedEntities[$entityClass];
//        $associatedEntities = [];
//        $isNewEntity = false;
//
//        if ($this->associationsCount > 0) {
//            foreach ($previousEntityClasses as $previousEntityClass) {
//                if (isset($this->associationClassToFieldNames[$previousEntityClass])) {
//                    if (null !== $syncedEntities[$previousEntityClass]) {
//                        $associatedEntities[] = $syncedEntities[$previousEntityClass];
//                    } else {
//                        return null;
//                    }
//                }
//            }
//        }
//
//        return $this->propertyAccessor->getValue($updater(new $entityClass, ...$associatedEntities), $keyColumn);
//    }

    public function sync(?callable $updater, string $keyColumn, ?array &$previousEntityClasses, ?array &$syncedEntities, ?array &$cachedEntities = null)
    {
        if (null === $updater) {
            return null;
        }

        $associationClassConfig = $this->associationClassConfig();

        $entityClass = $this->getEntityName();

        if (!isset($cachedEntities[$entityClass])) {
            $cachedEntities[$entityClass] = [];
        }

        $classMateEntities = $cachedEntities[$entityClass];
        $associatedEntities = [];
        $isNewEntity = false;

        if ($associationClassConfig['count'] > 0) {
            foreach ($previousEntityClasses as $previousEntityClass) {
                if (isset($associationClassConfig['classToFieldNames'][$previousEntityClass])) {
                    if (null !== $syncedEntities[$previousEntityClass]) {
                        $associatedEntities[] = $syncedEntities[$previousEntityClass];
                    } else {
                        return null;
                    }
                }
            }
        }

        $keyValue = $this->propertyAccessor->getValue($updater(new $entityClass, ...$associatedEntities), $keyColumn);

        if (!isset($classMateEntities[$keyValue])) {
            $criteria[$keyColumn] = $keyValue;
            $entity = $this->findOneBy($criteria);

            if (null === $entity) {
                $entity = new $entityClass;
                $isNewEntity = true;
            }

            $classMateEntities[$keyValue] = $entity;
        }

        $cachedEntities[$entityClass] = $classMateEntities;
        $entity = $updater($classMateEntities[$keyValue], ...$associatedEntities);

        if ($isNewEntity) {
            // start tracking entity & schedule for persisting
            $this->getEntityManager()->persist($entity);
        }

        return $entity;
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

    public function saveAll($items)
    {
        foreach ($items as $i => $item) {
            $this->save($item);
        }
    }

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

    protected function nullOrInt($stringValue): ?int
    {
        return '' === $stringValue ? null : (int)$stringValue;
    }

    protected function nullOrFloat($stringValue): ?float
    {
        return '' === $stringValue ? null : round((float)str_replace(',', '.', $stringValue), 3);
    }

    protected function commitChangesToDb()
    {
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    private function associationClassConfig()
    {
        $entityClass = $this->getEntityName();

        if (!isset($this->associationClassConfig[$entityClass])) {
            $classToFieldNames = [];

            foreach ($this->getClassMetadata()->associationMappings as $associationMapping) {
                // TODO: consider support for multiple associations of the same class
                // TODO: handle nullable associations
                $classToFieldNames[$associationMapping['targetEntity']] = true;
            }

            $this->associationClassConfig[$entityClass] = [
                'classToFieldNames' => $classToFieldNames,
                'count' => count($this->getClassMetadata()->associationMappings),
            ];
        }

        return $this->associationClassConfig[$entityClass];
    }
}
