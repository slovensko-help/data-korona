<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;

abstract class AbstractRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    public function findAllIndexedById()
    {
        $result = [];

        foreach ($this->findAll() as $entity) {
            $result[$entity->getId()] = $entity;
        }

        return $result;
    }

    public function updateAllFromQuery(callable $queryBuilderCallback, callable $updateRecordCallback) {

        $query = $queryBuilderCallback($this)->execute();

        $i = 0;

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $entityClass = $this->getEntityName();

        while ($record = $query->fetch()) {
            $record = $updateRecordCallback($record);

            $entity = $this->findOneBy(['id' => $record['id']]) ?? new $entityClass();

            foreach ($record as $columnName => $columnValue) {
                $propertyAccessor->setValue($entity, $columnName, $columnValue);
            }

            $this->getEntityManager()->persist($entity);

            if ($i % 1000 === 0) {
                $this->commitChangesToDb([$entityClass]);
            }

            $i++;
        }

        $this->commitChangesToDb([$entityClass]);
    }

    public function updateOrCreate(callable $updateEntityCallback, array $criteria, $flushAutomatically = false)
    {
        $entity = $updateEntityCallback($this->findOneBy($criteria));

        if (null !== $entity) {
            $this->getEntityManager()->persist($entity);

            if ($flushAutomatically) {
                $this->getEntityManager()->flush();
            }
        }

        return $entity;
    }

    public function aggregatableColumns(string $trait, string $entityClass)
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

    protected function commitChangesToDb(array $clearEntityClasses = [])
    {
        $this->getEntityManager()->flush();

        foreach ($clearEntityClasses as $clearEntityClasse) {
            $this->getEntityManager()->clear($clearEntityClasse);
        }
    }
}
