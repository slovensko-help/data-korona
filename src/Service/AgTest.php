<?php

namespace App\Service;

use DateTimeImmutable;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Types\Types;
use Exception;

class AgTest extends AbstractStatsService
{
    /**
     * @return array
     * @throws Exception
     */
    public function dailyStats(): array
    {
        $izaByDay = $this->indexBy('published_on',
            $this->withSums(
                $this->findAllIzaByDay(),
                [],
                ['positives_count' => 'positives_sum', 'negatives_count' => 'negatives_sum']
            )
        );

        $powerBiByDay = $this->indexBy('published_on',
            $this->withSums(
                $this->findAllPowerBiByDay(),
                [],
                ['positives_count' => 'positives_sum', 'negatives_count' => 'negatives_sum']
            )
        );

        $ncziByDay = $this->indexBy('published_on',
            $this->withDeltas(
                $this->findAllNcziByDay(),
                ['positives_sum' => 'positives_count', 'negatives_sum' => 'negatives_count'],
                []
            )
        );

        return $this->diff(
            $this->merge([
                'iza' => $izaByDay,
                'power_bi' => $powerBiByDay,
                'nczi' => $ncziByDay
            ]),
            [
                'positives_count' => ['power_bi', 'nczi', 'iza'],
                'negatives_count' => ['power_bi', 'nczi', 'iza'],
                'positives_sum' => ['power_bi', 'nczi', 'iza'],
                'negatives_sum' => ['power_bi', 'nczi', 'iza'],
            ]
        );
    }

    /**
     * @param DateTimeImmutable $from
     * @return array
     * @throws Exception
     */
    public function districtualDailyStats(DateTimeImmutable $from): array
    {
        $izaByDay = $this->indexBy('row_key',
            $this->withSums(
                $this->findAllIzaByDayAndDistrict($from),
                ['district_title'],
                ['positives_count' => 'positives_sum', 'negatives_count' => 'negatives_sum'],
                [
                    'key' => 'district_title',
                    'values' => $this->izaSumsByDayAndDistrictByDistrictTitle($from),
                ]
            )
        );

        $powerBiByDay = $this->indexBy('row_key',
            $this->withSums(
                $this->findAllPowerBiByDayAndDistrict($from),
                ['district_title'],
                ['positives_count' => 'positives_sum', 'negatives_count' => 'negatives_sum'],
                [
                    'key' => 'district_title',
                    'values' => $this->powerBiSumsByDayAndDistrictByDistrictTitle($from),
                ]
            )
        );

        return $this->diff(
            $this->merge([
                'iza' => $izaByDay,
                'power_bi' => $powerBiByDay
            ], ['published_on', 'district_title', 'district_id']
            ),
            [
                'positives_count' => ['power_bi', 'iza'],
                'negatives_count' => ['power_bi', 'iza'],
                'positives_sum' => ['power_bi', 'iza'],
                'negatives_sum' => ['power_bi', 'iza'],
            ]);
    }

    /**
     * @param DateTimeImmutable $from
     * @return iterable
     * @throws Exception
     */
    public function regionalDailyStats(DateTimeImmutable $from): array
    {
        $izaByDay = $this->indexBy('row_key',
            $this->withSums(
                $this->findAllIzaByDayAndRegion($from),
                ['region_title'],
                ['positives_count' => 'positives_sum', 'negatives_count' => 'negatives_sum'],
                [
                    'key' => 'region_title',
                    'values' => $this->izaSumsByDayAndDistrictByRegionTitle($from),
                ]
            )
        );

        $powerBiByDay = $this->indexBy('row_key',
            $this->withSums(
                $this->findAllPowerBiByDayAndRegion($from),
                ['region_title'],
                ['positives_count' => 'positives_sum', 'negatives_count' => 'negatives_sum'],
                [
                    'key' => 'region_title',
                    'values' => $this->powerBiSumsByDayAndDistrictByRegionTitle($from),
                ]
            )
        );

        return $this->diff(
            $this->merge([
                'iza' => $izaByDay,
                'power_bi' => $powerBiByDay
            ], ['published_on', 'region_title', 'region_id']
            ),
            [
                'positives_count' => ['power_bi', 'iza'],
                'negatives_count' => ['power_bi', 'iza'],
                'positives_sum' => ['power_bi', 'iza'],
                'negatives_sum' => ['power_bi', 'iza'],
            ]);
    }

    /**
     * @return iterable
     * @throws DBALException
     */
    public function updateStats(): iterable
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
                        raw_iza_ag_tests
                    LIMIT 1
                )
                UNION
                (
                    SELECT
                        \'NCZI (Power BI)\' AS source,
                        MAX(updated_at) AS last_updated_at,
                        MAX(published_on) AS last_published_on
                    FROM
                        raw_power_bi_ag_tests
                    LIMIT 1
                )
                UNION
                (
                    SELECT
                        \'NCZI (API)\' AS source,
                        MAX(updated_at) AS last_updated_at,
                        MAX(published_on) AS last_published_on
                    FROM
                        raw_nczi_ag_tests
                    LIMIT 1
                )
            ) AS t
        ')->fetchAll();
    }

    /**
     * @return iterable
     * @throws DBALException
     */
    private function findAllNcziByDay(): iterable
    {
        return $this->connection->query('
            SELECT
                published_on,
                updated_at,
                positives_sum,
                negatives_sum
            FROM
                raw_nczi_ag_tests
            GROUP BY 
                published_on
            ORDER BY
                published_on
        ')->fetchAll();
    }

    /**
     * @return iterable
     * @throws DBALException
     */
    private function findAllIzaByDay(): iterable
    {
        return $this->connection->query('
            SELECT
                published_on,
                updated_at,
                SUM(positives_count) AS positives_count,
                SUM(negatives_count) AS negatives_count
            FROM
                raw_iza_ag_tests
            GROUP BY 
                published_on
            ORDER BY
                published_on
        ')->fetchAll();
    }

    /**
     * @param DateTimeImmutable $from
     * @return iterable
     * @throws DBALException
     */
    private function findAllIzaByDayAndDistrict(DateTimeImmutable $from): iterable
    {
        $statement = $this->connection->prepare('
            SELECT
                CONCAT(v.published_on, \'-\', IFNULL(d.title, \'---\')) AS row_key,
                v.published_on,
                v.updated_at,
                IFNULL(d.id, 0) AS district_id,
                IFNULL(d.title, \'---\') AS district_title,
                SUM(v.positives_count) AS positives_count,
                SUM(v.negatives_count) AS negatives_count
            FROM
                raw_iza_ag_tests AS v 
            LEFT JOIN
                district AS d 
            ON
                v.district_id = d.id
            WHERE
                v.published_on >= :from
            GROUP BY 
                v.published_on, district_title
            ORDER BY
                v.published_on, district_title
        ');

        $statement->bindValue('from', $from, Types::DATE_IMMUTABLE);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param DateTimeImmutable $from
     * @return iterable
     * @throws DBALException
     */
    private function findAllIzaByDayAndRegion(DateTimeImmutable $from): iterable
    {
        $statement = $this->connection->prepare('
            SELECT
                CONCAT(v.published_on, \'-\', IFNULL(r.title, \'---\')) AS row_key,
                v.published_on,
                v.updated_at,
                IFNULL(r.id, 0) AS region_id,
                IFNULL(r.title, \'---\') AS region_title,
                SUM(v.positives_count) AS positives_count,
                SUM(v.negatives_count) AS negatives_count
            FROM
                raw_iza_ag_tests AS v 
            LEFT JOIN
                district AS d 
            ON
                v.district_id = d.id
            LEFT JOIN
                region AS r 
            ON
                d.region_id = r.id
            WHERE
                v.published_on >= :from
            GROUP BY 
                v.published_on, region_title
            ORDER BY
                v.published_on, region_title
        ');

        $statement->bindValue('from', $from, Types::DATE_IMMUTABLE);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param DateTimeImmutable $from
     * @return iterable
     * @throws DBALException
     */
    private function izaSumsByDayAndDistrictByDistrictTitle(DateTimeImmutable $from): iterable
    {
        $statement = $this->connection->prepare('
            SELECT
                IFNULL(d.title, \'---\') AS row_key,
                SUM(v.positives_count) AS positives_sum,
                SUM(v.negatives_count) AS negatives_sum
            FROM
                raw_iza_ag_tests AS v 
            LEFT JOIN
                district AS d 
            ON
                v.district_id = d.id
            WHERE
                v.published_on < :from
            GROUP BY 
                row_key
        ');

        $statement->bindValue('from', $from, Types::DATE_IMMUTABLE);
        $statement->execute();

        return $this->indexBy('row_key', $statement->fetchAll());
    }

    /**
     * @param DateTimeImmutable $from
     * @return iterable
     * @throws DBALException
     */
    private function izaSumsByDayAndDistrictByRegionTitle(DateTimeImmutable $from): iterable
    {
        $statement = $this->connection->prepare('
            SELECT
                IFNULL(r.title, \'---\') AS row_key,
                SUM(v.positives_count) AS positives_sum,
                SUM(v.negatives_count) AS negatives_sum
            FROM
                raw_iza_ag_tests AS v 
            LEFT JOIN
                district AS d 
            ON
                v.district_id = d.id
            LEFT JOIN
                region AS r 
            ON
                d.region_id = r.id
            WHERE
                v.published_on < :from
            GROUP BY 
                row_key
        ');

        $statement->bindValue('from', $from, Types::DATE_IMMUTABLE);
        $statement->execute();

        return $this->indexBy('row_key', $statement->fetchAll());
    }

    /**
     * @param DateTimeImmutable $from
     * @return iterable
     * @throws DBALException
     */
    private function powerBiSumsByDayAndDistrictByDistrictTitle(DateTimeImmutable $from): iterable
    {
        $statement = $this->connection->prepare('
            SELECT
                IFNULL(d.title, \'---\') AS row_key,
                SUM(v.positives_count) AS positives_sum,
                SUM(v.negatives_count) AS negatives_sum
            FROM
                raw_power_bi_ag_tests AS v 
            LEFT JOIN
                district AS d 
            ON
                v.district_id = d.id
            WHERE
                v.published_on < :from
            GROUP BY 
                row_key
        ');

        $statement->bindValue('from', $from, Types::DATE_IMMUTABLE);
        $statement->execute();

        return $this->indexBy('row_key', $statement->fetchAll());
    }

    /**
     * @param DateTimeImmutable $from
     * @return iterable
     * @throws DBALException
     */
    private function powerBiSumsByDayAndDistrictByRegionTitle(DateTimeImmutable $from): iterable
    {
        $statement = $this->connection->prepare('
            SELECT
                IFNULL(r.title, \'---\') AS row_key,
                SUM(v.positives_count) AS positives_sum,
                SUM(v.negatives_count) AS negatives_sum
            FROM
                raw_power_bi_ag_tests AS v 
            LEFT JOIN
                district AS d 
            ON
                v.district_id = d.id
            LEFT JOIN
                region AS r 
            ON
                d.region_id = r.id
            WHERE
                v.published_on < :from
            GROUP BY 
                row_key
        ');

        $statement->bindValue('from', $from, Types::DATE_IMMUTABLE);
        $statement->execute();

        return $this->indexBy('row_key', $statement->fetchAll());
    }

    /**
     * @return iterable
     * @throws DBALException
     */
    private function findAllPowerBiByDay(): iterable
    {
        return $this->connection->query('
            SELECT
                published_on,
                updated_at,
                SUM(positives_count) AS positives_count,
                SUM(negatives_count) AS negatives_count
            FROM
                raw_power_bi_ag_tests
            GROUP BY 
                published_on
            ORDER BY
                published_on
        ')->fetchAll();
    }

    /**
     * @param DateTimeImmutable $from
     * @return iterable
     * @throws DBALException
     */
    private function findAllPowerBiByDayAndDistrict(DateTimeImmutable $from): iterable
    {
        $statement = $this->connection->prepare('
            SELECT
               CONCAT(v.published_on, \'-\', d.title) AS row_key,
                v.published_on,
                v.updated_at,
                d.title AS district_title,
                d.id AS district_id,
                SUM(v.positives_count) AS positives_count,
                SUM(v.negatives_count) AS negatives_count
            FROM
                raw_power_bi_ag_tests AS v
            INNER JOIN
                district AS d 
            ON
                v.district_id = d.id
            WHERE
                v.published_on >= :from
            GROUP BY 
                v.published_on, d.title
            ORDER BY
                v.published_on, d.title
        ');

        $statement->bindValue('from', $from, Types::DATE_IMMUTABLE);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @param DateTimeImmutable $from
     * @return iterable
     * @throws DBALException
     */
    private function findAllPowerBiByDayAndRegion(DateTimeImmutable $from): iterable
    {
        $statement = $this->connection->prepare('
            SELECT
               CONCAT(v.published_on, \'-\', r.title) AS row_key,
                v.published_on,
                v.updated_at,
                r.title AS region_title,
                r.id AS region_id,
                SUM(v.positives_count) AS positives_count,
                SUM(v.negatives_count) AS negatives_count
            FROM
                raw_power_bi_ag_tests AS v
            INNER JOIN
                district AS d 
            ON
                v.district_id = d.id
            INNER JOIN
                region AS r 
            ON
                d.region_id = r.id
            WHERE
                v.published_on >= :from
            GROUP BY 
                v.published_on, region_title
            ORDER BY
                v.published_on, region_title
        ');

        $statement->bindValue('from', $from, Types::DATE_IMMUTABLE);
        $statement->execute();

        return $statement->fetchAll();
    }
}
