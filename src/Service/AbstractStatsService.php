<?php

namespace App\Service;

use Doctrine\DBAL\Connection;

class AbstractStatsService
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    protected function diff(array $input, array $diffFields): array
    {
        $result = $input;
        $nullIndex = 0;

        foreach ($diffFields as $diffField => $collectionKeys) {
            $max_uniques[$diffField] = count($collectionKeys);
            foreach ($input as $rowIndex => $row) {
                foreach ($collectionKeys as $collectionKey) {
                    $value = isset($row[$collectionKey]) && isset($row[$collectionKey][$diffField]) ? $row[$collectionKey][$diffField] : 'null';
                    if (!isset($result[$rowIndex]['uniques'][$diffField][$value])) {
                        $result[$rowIndex]['uniques'][$diffField][$value] = [];
                    }
                    $result[$rowIndex]['uniques'][$diffField][$value][] = $collectionKey;

                    if ('null' !== $value) {
                        if (!isset($result[$rowIndex]['uniques_without_null'][$diffField][$value])) {
                            $result[$rowIndex]['uniques_without_null'][$diffField][$value] = [];
                        }
                        $result[$rowIndex]['uniques_without_null'][$diffField][$value][] = $collectionKey;
                    }

                    $result[$rowIndex]['max_uniques'][$diffField] = $max_uniques[$diffField];

                    $nullIndex++;
                }
            }
        }

        return $result;
    }

    protected function merge(array $collections, array $commonKeys = [])
    {
        $itemKeys = [];

        foreach ($collections as $collection) {
            foreach ($collection as $key => $item) {
                $itemKeys[$key] = $key;
            }
        }

        $itemKeys = array_values($itemKeys);
        rsort($itemKeys);

        $result = [];
        foreach ($itemKeys as $itemKey) {
            $data = ['key' => $itemKey];
            foreach ($collections as $collectionKey => $collection) {
                foreach ($commonKeys as $commonKey) {
                    $data[$commonKey] = isset($collection[$itemKey]) && isset($collection[$itemKey][$commonKey]) ? $collection[$itemKey][$commonKey] : ($data[$commonKey] ?? null);
                }

                $data[$collectionKey] = $collection[$itemKey] ?? null;
            }

            $result[] = $data;
        }

        return $result;
    }

    protected function withSums(iterable $collection, array $sumGroupFields = [], array $sumFields = [], array $initialSums = []): array
    {
        if (empty($sumFields)) {
            throw new \Exception('$sumFields cannot be empty.');
        }

        $result = [];
        $sums = [];

        foreach ($sumFields as $sourceField => $sumField) {
            $sums[$sumField] = [];
        }

        foreach ($collection as $row) {
            $data = $row;

            $sumGroupKeys = [];

            foreach ($sumGroupFields as $sumGroupField) {
                $sumGroupKeys[] = $row[$sumGroupField];
            }

            $sumGroupKey = implode(',', $sumGroupKeys);

            foreach ($sumFields as $sourceField => $sumField) {
                if (!isset($sums[$sumField][$sumGroupKey])) {

                    if (isset($initialSums['key']) && isset($initialSums['values'][$row[$initialSums['key']]])) {
                        $sums[$sumField][$sumGroupKey] = $initialSums['values'][$row[$initialSums['key']]][$sumField];
                    }
                    else {
                        $sums[$sumField][$sumGroupKey] = 0;
                    }
                }

                $sums[$sumField][$sumGroupKey] += (int)$row[$sourceField];
                $data[$sumField] = $sums[$sumField][$sumGroupKey];
            }

            $result[] = $data;
        }

        return $result;
    }

    protected function withDeltas(iterable $collection, array $deltaFields, array $groupFields = []): array
    {
        $result = [];
        $previousValues = [];

        foreach ($collection as $row) {
            $data = $row;

            $groupKeys = ['-'];

            foreach ($groupFields as $groupField) {
                $groupKeys[] = $row[$groupField];
            }

            $groupKey = implode(',', $groupKeys);

            if (!isset($previousValues[$groupKey])) {
                foreach ($deltaFields as $deltaField => $newField) {
                    $previousValues[$groupKey][$deltaField] = $data[$deltaField];
                }
            }
            else {
                foreach ($deltaFields as $deltaField => $newField) {
                    $data[$newField] = (int)$data[$deltaField] - (int)$previousValues[$groupKey][$deltaField];
                    $previousValues[$groupKey][$deltaField] = $data[$deltaField];
                }
            }

            $result[] = $data;
        }

        return $result;
    }

    protected function indexBy(string $key, iterable $collection): array
    {
        $result = [];

        foreach ($collection as $item) {
            $result[$item[$key]] = $item;
        }

        return $result;
    }
}
