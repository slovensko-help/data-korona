<?php

namespace App\Persister;

use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Generator;
use ReflectionFunction;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class EntityPersister
{
    const FIRST_BATCH = 1;
    const LAST_BATCH = 2;

    // all entities tracked by orm (as 2 dimensional array [class][key])
    private $trackedEntities = [];

    // persisted entities (or null values) (as 2 dimensional array [row][column])
    private $entityTable = [];

    private $associatedEntities = [];
    private $updaterTable = [];
    private $entityKeys = [];
    private $classKeys = [];
    private $maxKeys = [];
    private $minKeys = [];

    private $closed = false;

    private $entityManager;
    private $propertyAccessor;

    public function __construct(EntityManagerInterface $entityManager, PropertyAccessorInterface $propertyAccessor)
    {
        $this->entityManager = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function persist(iterable $rows, callable $entityUpdatersGenerator, ?array $deletionConfig = null, int $batchSize = 512)
    {
        if ($this->closed) {
            throw new Exception('Cannot persist closed persister.');
        }

        $entitiesConfig = $this->initializeEntitiesConfig($entityUpdatersGenerator, $deletionConfig);
        $isFirstBatch = true;
        $previousEntityKeys = [];

        foreach ($this->batches($rows, $batchSize) as $rows) {
            $this->trackedEntities = [];
            $this->associatedEntities = [];
            $this->entityTable = [];
            $this->updaterTable = [];
            $this->entityKeys = [];

            foreach ($entitiesConfig as $colIndex => $entityConfig) {
                $entityKeys = [];
                $this->minKeys[$entityConfig['class']] = $this->maxKeys[$entityConfig['class']] ?? null;

                foreach ($rows as $rowIndex => $row) {
                    if (0 === $colIndex) {
                        $this->initializeRowEntityUpdaters($rowIndex, $entityUpdatersGenerator($row));
                    }

                    $entityKey = $this->entityKey($rowIndex, $colIndex, $entityConfig);

//                    if (!$entityConfig['isReadonlyMode'] && null !== $entityConfig['deletions'] && isset($previousEntityKeys[$colIndex]) && $previousEntityKeys[$colIndex] >= $entityKey) {
//                        throw new Exception('Automatically deletable entities are not sorted by "' . $entityConfig['keyField'] . '" field. ' .
//                            'Previous key was "' . $previousEntityKeys[$colIndex] . '", current key is "' . $entityKey . '". ' .
//                            'Previous key must be always less than current key.');
//                    }

                    $previousEntityKeys[$colIndex] = $entityKey;

                    if (null !== $entityKey) {
                        if (!isset($this->maxKeys[$entityConfig['class']]) || $entityKey > $this->maxKeys[$entityConfig['class']]) {
                            $this->maxKeys[$entityConfig['class']] = $entityKey;
                        }

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

                $this->entityManager->flush();
            }

            //$this->deleteMissingEntities($entitiesConfig, $isFirstBatch ? static::FIRST_BATCH : 0);
            $isFirstBatch = false;

            $this->entityManager->clear();
        }

        //$this->deleteMissingEntities($entitiesConfig, static::LAST_BATCH | ($isFirstBatch ? static::FIRST_BATCH : 0));
        $this->entityManager->clear();

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
            $entity = $this->persistAndTrackEntity($rowIndex, $colIndex, $entity, $entityConfig);
        }

        return $entity;
    }

    public function entityKey(int $rowIndex, int $colIndex, array $entityConfig)
    {
        $this->associatedEntities[$rowIndex][$colIndex] = [];

        foreach ($entityConfig['associationIndices'] as $associationIndex) {
            $this->associatedEntities[$rowIndex][$colIndex][] = $this->entityTable[$rowIndex][$associationIndex] ?? null;
        }

        $entity = $this->updaterTable[$rowIndex][$colIndex](new $entityConfig['class'], ...$this->associatedEntities[$rowIndex][$colIndex]);

        if (null === $entity) {
            return null;
        }

        $key = $this->propertyAccessor->getValue($entity, $entityConfig['keyField']);;

        if (!isset($this->classKeys[$entityConfig['class']])) {
            $this->classKeys[$entityConfig['class']] = [];
        }

        $this->classKeys[$entityConfig['class']][] = $key;

        return $this->entityKeys[$rowIndex][$colIndex] = $key;
    }

    protected function batches(iterable $dataItems, $batchSize = 128)
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

    private function initializeEntitiesConfig(callable $entityUpdatersGenerator, ?array $deletionConfig = null): array
    {
        $parameters = (new ReflectionFunction($entityUpdatersGenerator))->getParameters();

        if (1 !== count($parameters)) {
            throw new Exception('Mapping generator function must have one parameter.');
        }

        if (null === $parameters[0]->getType()) {
            throw new Exception('Mapping generator function parameter must have type annotation in ' . $parameters[0]->getDeclaringClass()->getName() . '.');
        }

        if ('array' === $parameters[0]->getType()->getName()) {
            $initValue = [];
        } elseif (class_exists($parameters[0]->getType()->getName())) {
            $initClassName = $parameters[0]->getType()->getName();
            $initValue = new $initClassName();
        } else {
            throw new Exception('Mapping generator function parameter must be array or object.');
        }

        $entitiesMapping = $entityUpdatersGenerator($initValue);

        $result = [];
        $classPositions = [];
        $index = 0;

        foreach ($entitiesMapping as $rawKeyField => $entityMappingCallback) {
            if (empty($rawKeyField) || $this->isInt($rawKeyField)) {
                throw new Exception('Entity key field "' . $rawKeyField . '" is not valid');
            }

            $keyFieldParts = explode(':', $rawKeyField);

            $keyField = $keyFieldParts[0];
            $isReadonlyMode = $keyFieldParts[1] ?? 'write' === 'readonly';

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

            if (isset($deletionConfig[$class])) {
                $deletions = [
                    'startKey' => $deletionConfig[$class][0],
                    'endKey' => $deletionConfig[$class][1],
                    'onlyIfNotEmpty' => $deletionConfig[$class][2] ?? false,
                ];
            } else {
                $deletions = null;
            }

            $result[] = [
                'repository' => $this->entityManager->getRepository($class),
                'isReadonlyMode' => $isReadonlyMode,
                'class' => $class,
                'keyField' => $keyField,
                'associationIndices' => $associationIndices,
                'deletions' => $deletions,
            ];

            $classPositions[$class][] = $index;

            $index++;
        }

        return $result;
    }

    private function deleteMissingEntities(array $entitiesConfig, int $flags = 0)
    {
        foreach ($entitiesConfig as $entityConfig) {
            if (null !== $entityConfig['deletions']) {
                if ($this->hasMultipleEntitiesOfTheSameClass($entityConfig['class'], $entitiesConfig)) {
                    throw new Exception('There are multiple entities of class ' . $entityConfig['class'] . '. Automatic deletion of missing entities is not possible.');
                }

                if (isset($this->maxKeys[$entityConfig['class']])) {
                    if ($flags & static::FIRST_BATCH) {
                        $minKey = $entityConfig['deletions']['startKey'];
                        $this->minKeys[$entityConfig['class']] = $minKey;
                    } else {
                        $minKey = $this->minKeys[$entityConfig['class']];
                    }

                    if ($flags & static::LAST_BATCH) {
                        $maxKey = $entityConfig['deletions']['endKey'];
                        $this->maxKeys[$entityConfig['class']] = $maxKey;
                    } else {
                        $maxKey = $this->maxKeys[$entityConfig['class']];
                    }

                    if ($entityConfig['deletions']['onlyIfNotEmpty'] && empty($this->classKeys[$entityConfig['class']])) {
                        continue;
                    }

                    $missingEntities = $entityConfig['repository']->findAllByKeyForDeletion(
                        $entityConfig['keyField'],
                        $this->classKeys[$entityConfig['class']],
                        $minKey,
                        $maxKey
                    );

                    foreach ($missingEntities as $missingEntity) {
                        $this->entityManager->remove($missingEntity);
                    }
                }
            }
        }

        $this->entityManager->flush();
    }

    private function hasMultipleEntitiesOfTheSameClass(string $entityClass, array $entitiesConfig)
    {
        $count = 0;

        foreach ($entitiesConfig as $entityConfig) {
            if ($entityConfig['class'] === $entityClass) {
                $count++;
            }

            if ($count > 1) {
                return true;
            }
        }

        return false;
    }

    private function isInt($value)
    {
        return strval($value) === strval(intval($value));
    }

    private function persistAndTrackEntity(int $rowIndex, int $colIndex, object $entity, array $entityConfig): ?object
    {
        $this->entityKeys[$rowIndex][$colIndex] = $this->propertyAccessor->getValue($entity, $entityConfig['keyField']);

        if ($entityConfig['isReadonlyMode']) {
            $this->trackedEntities[$entityConfig['class']][$this->entityKeys[$rowIndex][$colIndex]] = null;
        } else {
            $this->entityManager->persist($entity);
            $this->trackedEntities[$entityConfig['class']][$this->entityKeys[$rowIndex][$colIndex]] = $entity;
        }

        return $this->trackedEntities[$entityConfig['class']][$this->entityKeys[$rowIndex][$colIndex]];
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
