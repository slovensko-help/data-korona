<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    public function relatedClasses() {
        dump($this->getClassMetadata()->associationMappings);
//        die;
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
        throw new Exception('Save method must be implemented in inherited class.');
    }

    public function saveAll($items)
    {
        foreach ($items as $i => $item) {
            $this->save($item);
        }
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
}
