<?php

namespace App\Persister;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Generator;
use ReflectionFunction;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

final class EntityPersister
{
    // all entities tracked by orm in 2 dimensional array [class][key]
    private $trackedEntities = [];

    // persisted entities (or null values) in 2 dimensional array [row][column]
    private $entityTable = [];

    private $associatedEntities = [];
    private $updaterTable = [];
    private $entityKeyValues = [];

    private $closed = false;

    private $entityManager;
    private $propertyAccessor;

    public function __construct(EntityManagerInterface $entityManager, PropertyAccessorInterface $propertyAccessor)
    {
        $this->entityManager = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function initializeEntitiesConfig(Generator $entitiesMapping): array
    {
        $result = [];
        $classPositions = [];
        $index = 0;

        foreach ($entitiesMapping as $entityKey => $entityMappingCallback) {
            $parameters = (new ReflectionFunction($entityMappingCallback))->getParameters();

            if (0 === count($parameters)) {
                throw new Exception('Mapping function has no entity parameter.');
            }

            if (null === $parameters[0]->getType() || !class_exists($parameters[0]->getType()->getName())) {
                throw new Exception('Mapping function entity is not an object. Did you forget to use type annotation?');
            }

            $classIndices = [];
            $associationIndices = [];

            for ($f = 1; $f < count($parameters); $f++) {
                $parameter = $parameters[$f];
                if (null === $parameter->getType() || !class_exists($parameter->getType()->getName())) {
                    throw new Exception('Associated entity $' . $parameter->getName() . ' is not an object. Did you forget to use type annotation?');
                }

                $associationClass = $parameter->getType()->getName();

                if (!isset($classPositions[$associationClass])) {
                    throw new Exception('Associated entity $' . $parameter->getName() . ' is not mapped before. No associated entity of class "' . $associationClass . '" found.');
                }

                if (!isset($classIndices[$associationClass])) {
                    $classIndices[$associationClass] = 0;
                }
                $classIndices[$associationClass]++;

                $classCount = count($classPositions[$associationClass]);

                if ($classCount < $classIndices[$associationClass]) {
                    throw new Exception('Associated entity $' . $parameter->getName() . ' is not mapped before. Only ' . $classCount . ' entity(ies) of class "' . $associationClass . '" found.');
                }

                $associationIndices[] = $classPositions[$associationClass][$classIndices[$associationClass] - 1];
            }

            $entityClass = $parameters[0]->getType()->getName();

            $result[] = [
                'repository' => $this->entityManager->getRepository($entityClass),
                'entityClass' => $entityClass,
                'entityKeyField' => $entityKey,
                'hasAssociations' => count($associationIndices) > 0,
                'associationIndices' => $associationIndices,
            ];

            $classPositions[$entityClass][] = $index;

            $index++;
        }

        return $result;
    }

    public function persist(Generator $rows, callable $entityUpdatersGenerator, int $batchSize = 128)
    {
        if ($this->closed) {
            throw new Exception('Cannot persist closed persister.');
        }

        $entitiesConfig = $this->initializeEntitiesConfig($entityUpdatersGenerator([]));

        foreach ($this->batches($rows, $batchSize) as $rows) {
            $this->trackedEntities = [];
            $this->entityTable = [];
            $this->associatedEntities = [];
            $this->updaterTable = [];
            $this->entityKeyValues = [];

            foreach ($entitiesConfig as $colIndex => $entityConfig) {
                $entityKeyValues = [];
                foreach ($rows as $rowIndex => $row) {
                    if (0 === $colIndex) {
                        $this->initializeRowEntityUpdaters($rowIndex, $entityUpdatersGenerator($row));
                    }

                    $entityKeyValue = $this->entityKey($rowIndex, $colIndex, $entityConfig);

                    if (null !== $entityKeyValue) {
                        $entityKeyValues[$entityKeyValue] = $entityKeyValue;
                    }
                }

                $this->trackedEntities[$entityConfig['entityClass']] = $entityConfig['repository']->findAllByKey(
                    $entityConfig['entityKeyField'],
                    array_values($entityKeyValues)
                );

                foreach ($rows as $rowIndex => $row) {
                    $this->entityTable[$rowIndex][$colIndex] = $this->persistedEntity($rowIndex, $colIndex, $entityConfig);
                }

//                dump(memory_get_usage() / 1024 / 1024);
            }

            $this->entityManager->flush();
            $this->entityManager->clear();
        }

        $this->closed = true;
    }

    public function persistedEntity(int $rowIndex, int $colIndex, array $entityConfig): ?object
    {
        if (!isset($this->entityKeyValues[$rowIndex][$colIndex])) {
            return null;
        }

        $entity = $this->trackedEntity($entityConfig['entityClass'], $this->entityKeyValues[$rowIndex][$colIndex]);

        if (null === $entity) {
            $entity = new $entityConfig['entityClass'];
            $doPersist = true;
        } else {
            $doPersist = false;
        }

        $entity = $this->updaterTable[$rowIndex][$colIndex]($entity, ...$this->associatedEntities[$rowIndex][$colIndex]);

        if ($doPersist) {
            $this->entityManager->persist($entity);
        }

        return $entity;
    }

    public function entityKey(int $rowIndex, int $colIndex, array $entityConfig)
    {
        if (null === $this->updaterTable[$rowIndex][$colIndex]) {
            return null;
        }

        $this->associatedEntities[$rowIndex][$colIndex] = [];

        foreach ($entityConfig['associationIndices'] as $associationIndex) {
            if (!isset($this->entityTable[$rowIndex][$associationIndex])) {
                return null;
            }
            $this->associatedEntities[$rowIndex][$colIndex][] = $this->entityTable[$rowIndex][$associationIndex];
        }

        return $this->entityKeyValues[$rowIndex][$colIndex] = $this->propertyAccessor->getValue(
            $this->updaterTable[$rowIndex][$colIndex](
                new $entityConfig['entityClass'],
                ...$this->associatedEntities[$rowIndex][$colIndex]
            ),
            $entityConfig['entityKeyField']
        );
    }

    protected function batches(Generator $dataItems, $batchSize = 128)
    {
        $result = [];
        $index = 0;
        foreach ($dataItems as $item) {
            $result[] = $item;

            if (0 === ++$index % $batchSize) {
                $index = 0;
                yield $result;
                $result = [];
            }
        }

        if (count($result) > 0) {
            yield $result;
        }
    }

    protected function trackedEntity(string $entityClass, string $entityKey): ?object
    {
        if (isset($this->trackedEntities[$entityClass])) {
            return $this->trackedEntities[$entityClass][$entityKey] ?? null;
        }

        return null;
    }

    private function initializeRowEntityUpdaters(int $rowIndex, Generator $entityUpdaters): void
    {
        $colIndex = 0;

        // $entitiesMapping may have non unique keys!
        foreach ($entityUpdaters as $entityUpdater) {
            $this->updaterTable[$rowIndex][$colIndex++] = $entityUpdater;
        }
    }
}