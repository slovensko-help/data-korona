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
    private $entityKeys = [];

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

        foreach ($entitiesMapping as $keyField => $entityMappingCallback) {
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

            $class = $parameters[0]->getType()->getName();

            $result[] = [
                'repository' => $this->entityManager->getRepository($class),
                'class' => $class,
                'keyField' => $keyField,
                'associationIndices' => $associationIndices,
            ];

            $classPositions[$class][] = $index;

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
            $this->associatedEntities = [];
            $this->entityTable = [];
            $this->updaterTable = [];
            $this->entityKeys = [];

            foreach ($entitiesConfig as $colIndex => $entityConfig) {
                $entityKeys = [];
                foreach ($rows as $rowIndex => $row) {
                    if (0 === $colIndex) {
                        $this->initializeRowEntityUpdaters($rowIndex, $entityUpdatersGenerator($row));
                    }

                    $entityKey = $this->entityKey($rowIndex, $colIndex, $entityConfig);

                    if (null !== $entityKey) {
                        $entityKeys[$entityKey] = $entityKey;
                    }
                }

                $this->trackedEntities[$entityConfig['class']] = $entityConfig['repository']->findAllByKey(
                    $entityConfig['keyField'],
                    array_values($entityKeys)
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
        if (!isset($this->entityKeys[$rowIndex][$colIndex])) {
            return null;
        }

        $entity = $this->trackedEntity($entityConfig['class'], $this->entityKeys[$rowIndex][$colIndex]);

        if (null === $entity) {
            $entity = new $entityConfig['class'];
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
        $this->associatedEntities[$rowIndex][$colIndex] = [];

        foreach ($entityConfig['associationIndices'] as $associationIndex) {
            if (!isset($this->entityTable[$rowIndex][$associationIndex])) {
                return null;
            }
            $this->associatedEntities[$rowIndex][$colIndex][] = $this->entityTable[$rowIndex][$associationIndex];
        }

        $entity = $this->updaterTable[$rowIndex][$colIndex](new $entityConfig['class'], ...$this->associatedEntities[$rowIndex][$colIndex]);

        if (null === $entity) {
            return null;
        }

        return $this->entityKeys[$rowIndex][$colIndex] = $this->propertyAccessor->getValue($entity, $entityConfig['keyField']);
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