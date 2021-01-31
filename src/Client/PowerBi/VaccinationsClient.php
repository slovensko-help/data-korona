<?php

namespace App\Client\PowerBi;

use App\QueryBuilder\Hint\DatePaginationHint;
use App\QueryBuilder\PowerBiQueryBuilder;
use DateTimeImmutable;
use Generator;

class VaccinationsClient extends AbstractClient
{
    const REPORT_URL = 'https://app.powerbi.com//view?r=eyJrIjoiMzk4ZmRjNmEtYmZiNC00NGRiLWE2NDEtNWRjNjFhMDM4Nzk1IiwidCI6IjMxMGJhNTk1LTAxM2MtNDAyZC05ZWYyLWI1N2Q1ZjFkY2Q2MyIsImMiOjl9';

    public function findAllByRegion(): Generator
    {
        foreach ($this->items($this->createQueryBuilder()
            ->selectColumn('COVID-19 Vakciny', 'DATUM_VYPL')
            ->selectColumn('COVID-19 Vakciny', 'SIDZAR_KRAJ_KOD_ST')
            ->selectColumn('COVID-19 Vakciny', 'ZASOBY_APLIKOVANE_1', PowerBiQueryBuilder::AGGREGATION_SUM)
            ->selectColumn('COVID-19 Vakciny', 'ZASOBY_APLIKOVANE_2', PowerBiQueryBuilder::AGGREGATION_SUM)
            ->selectColumn('COVID-19 Vakciny', 'VAKC_VYROBCA_POPIS')
            ->selectColumn('COVID-19 Vakciny', 'VAKC_POPIS'),
            new DatePaginationHint('COVID-19 Vakciny', 'DATUM_VYPL', DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01'), 60)
        ) as $responseItem) {
            yield $this->hydrateVaccinations($responseItem);
        }
    }

    private function hydrateVaccinations(array $data): array
    {
        return [
            'published_on' => (new DateTimeImmutable())->setTimestamp($data[0] / 1000),
            'region_code' => $data[1],
            'dose_1_count' => $data[2],
            'dose_2_count' => $data[3],
            'vaccine_manufacturer_name' => $data[4],
            'vaccine_name' => $data[5],
        ];
    }
}