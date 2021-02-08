<?php

namespace App\Service;

use Doctrine\DBAL\Connection;

class Vaccination
{
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function dailyStats()
    {
        $izaByDay = $this->indexBy('published_on',
            $this->withSums(
                $this->findAllIzaByDay(),
                []
            )
        );

        $powerBiByDay = $this->indexBy('published_on',
            $this->withSums(
                $this->findAllPowerBiByDay(),
                []
            )
        );

        $ncziByDay = $this->indexBy('published_on',
            $this->findAllNcziByDay()
        );

        return $this->diff(
            $this->merge([
                'iza' => $izaByDay,
                'power_bi' => $powerBiByDay,
                'nczi' => $ncziByDay
            ]),
            [
                'dose1_count' => ['power_bi', 'iza'],
                'dose2_count' => ['power_bi', 'iza'],
                'dose1_sum' => ['power_bi', 'nczi', 'iza'],
                'dose2_sum' => ['power_bi', 'nczi', 'iza'],
            ]
        );
    }

    public function regionalDailyStats()
    {
        $izaByDay = $this->indexBy('row_key',
            $this->withSums(
                $this->findAllIzaByDayAndRegion(),
                ['region_title']
            )
        );

        $powerBiByDay = $this->indexBy('row_key',
            $this->withSums(
                $this->findAllPowerBiByDayAndRegion(),
                ['region_title']
            )
        );

        return $this->diff(
            $this->merge([
                'iza' => $izaByDay,
                'power_bi' => $powerBiByDay
            ], ['published_on', 'region_title']
            ),
            [
                'dose1_count' => ['power_bi', 'iza'],
                'dose2_count' => ['power_bi', 'iza'],
                'dose1_sum' => ['power_bi', 'iza'],
                'dose2_sum' => ['power_bi', 'iza'],

            ]);
    }

    public function updateStats()
    {
        return $this->connection->query('
            SELECT
                t.source,
                t.last_updated_at,
                t.last_published_on
            FROM
            (
                (
                    SELECT
                        \'IZA (GitHub)\' AS source,
                        MAX(updated_at) AS last_updated_at,
                        MAX(published_on) AS last_published_on
                    FROM
                        raw_iza_vaccinations
                    LIMIT 1
                )
                UNION
                (
                    SELECT
                        \'NCZI (Power BI)\' AS source,
                        MAX(updated_at) AS last_updated_at,
                        MAX(published_on) AS last_published_on
                    FROM
                        raw_power_bi_vaccinations
                    LIMIT 1
                )
                UNION
                (
                    SELECT
                        \'NCZI (API)\' AS source,
                        MAX(updated_at) AS last_updated_at,
                        MAX(published_on) AS last_published_on
                    FROM
                        raw_nczi_vaccinations
                    LIMIT 1
                )
            ) AS t
        ')->fetchAll();
    }

    private function diff(array $input, array $diffFields): array
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

    private function merge(array $collections, array $commonKeys = [])
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

    private function findAllNcziByDay()
    {
        return $this->connection->query('
            SELECT
                published_on,
                dose1_sum,
                dose2_sum
            FROM
                raw_nczi_vaccinations
            GROUP BY 
                published_on
            ORDER BY
                published_on ASC
        ')->fetchAll();
    }

    private function findAllIzaByDay()
    {
        return $this->connection->query('
            SELECT
                published_on,
                SUM(dose1_count) AS dose1_count,
                SUM(dose2_count) AS dose2_count
            FROM
                raw_iza_vaccinations
            GROUP BY 
                published_on
            ORDER BY
                published_on ASC
        ')->fetchAll();
    }

    private function findAllIzaByDayAndRegion()
    {
        return $this->connection->query('
            SELECT
                CONCAT(v.published_on, \'-\', r.title) AS row_key,
                v.published_on,
                r.title AS region_title,
                SUM(v.dose1_count) AS dose1_count,
                SUM(v.dose2_count) AS dose2_count
            FROM
                raw_iza_vaccinations AS v 
            INNER JOIN
                region AS r 
            ON
                v.region_id = r.id
            GROUP BY 
                v.published_on, r.title
            ORDER BY
                v.published_on ASC, r.title ASC
        ')->fetchAll();
    }

    private function findAllPowerBiByDay()
    {
        return $this->connection->query('
            SELECT
                published_on,
                SUM(dose1_count) AS dose1_count,
                SUM(dose2_count) AS dose2_count
            FROM
                raw_power_bi_vaccinations
            GROUP BY 
                published_on
            ORDER BY
                published_on ASC
        ')->fetchAll();
    }

    private function findAllPowerBiByDayAndRegion()
    {
        return $this->connection->query('
            SELECT
               CONCAT(v.published_on, \'-\', r.title) AS row_key,
                v.published_on,
               r.title AS region_title,
                SUM(v.dose1_count) AS dose1_count,
                SUM(v.dose2_count) AS dose2_count
            FROM
                raw_power_bi_vaccinations AS v
            INNER JOIN
                region AS r 
            ON
                v.region_id = r.id
            GROUP BY 
                v.published_on, r.title
            ORDER BY
                v.published_on ASC, r.title ASC
        ')->fetchAll();
    }

    private function withSums(iterable $collection, array $sumGroupFields = []): array
    {
        $result = [];
        $dose1Sums = [];
        $dose2Sums = [];

        foreach ($collection as $row) {
            $data = $row;

            $sumGroupKeys = [];

            foreach ($sumGroupFields as $sumGroupField) {
                $sumGroupKeys[] = $row[$sumGroupField];
            }

            $sumGroupKey = implode(',', $sumGroupKeys);

            if (!isset($dose1Sums[$sumGroupKey])) {
                $dose1Sums[$sumGroupKey] = 0;
            }

            if (!isset($dose2Sums[$sumGroupKey])) {
                $dose2Sums[$sumGroupKey] = 0;
            }

            $dose1Sums[$sumGroupKey] += (int)$row['dose1_count'];
            $dose2Sums[$sumGroupKey] += (int)$row['dose2_count'];

            $data['dose1_sum'] = $dose1Sums[$sumGroupKey];
            $data['dose2_sum'] = $dose2Sums[$sumGroupKey];

            $result[] = $data;
        }

        return $result;
    }

    private function indexBy(string $key, iterable $collection): array
    {
        $result = [];

        foreach ($collection as $item) {
            $result[$item[$key]] = $item;
        }

        return $result;
    }
}