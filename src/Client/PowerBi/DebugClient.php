<?php

namespace App\Client\PowerBi;

use App\QueryBuilder\Hint\DatePaginationHint;
use App\QueryBuilder\PowerBiQueryBuilder;
use DateTimeImmutable;
use Generator;

class DebugClient extends AbstractClient
{
    const REPORT_URL = 'https://app.powerbi.com//view?r=eyJrIjoiMzk4ZmRjNmEtYmZiNC00NGRiLWE2NDEtNWRjNjFhMDM4Nzk1IiwidCI6IjMxMGJhNTk1LTAxM2MtNDAyZC05ZWYyLWI1N2Q1ZjFkY2Q2MyIsImMiOjl9';

    public function debug(): Generator
    {
        yield from $this->all($this->createQueryBuilder()
            ->selectColumn('Ag testy', 'DATUM_VYPL_F')
            ->selectColumn('Ag testy', 'AGTEST_OKRES_KOD_ST')
            ->selectColumn('Ag testy', 'AGTEST_OKRES_POPIS')
//            ->selectColumn('Ag testy', 'AGTEST_KRAJ_POPIS')

//            ->selectColumn('Ag testy', 'ANTG_TESTY_VYSLEDOK')
//            ->selectColumn('Ag testy', 'ANTG_TESTY_POCET', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_ZP', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_VEREJNOST', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_PLOSNE', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_PACIENTI', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_NZP', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_INIOKRUH', PowerBiQueryBuilder::AGGREGATION_SUM)


            ->selectMeasure('Ag testy', 'Negativne')
            ->selectMeasure('Ag testy', 'Pozitivne')
            ->selectMeasure('Ag testy', '% negat')
            ->selectMeasure('Ag testy', '% pozit')
            ->orderBy('Ag testy', 'DATUM_VYPL_F', PowerBiQueryBuilder::ORDER_ASC)
//            ->orderBy('Ag testy', 'AGTEST_OKRES_POPIS', PowerBiQueryBuilder::ORDER_ASC)

//            ->selectColumn('Umrtia', 'DATUM_ZAR')
//            ->selectColumn('Umrtia', 'POC_VYLIECENI', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Umrtia', 'POC_NEAKTIVNI', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Umrtia', 'POC_AKTIVNI', PowerBiQueryBuilder::AGGREGATION_SUM)

//            ->selectColumn('COVID-19 Vakciny', 'ZASOBY_APLIKOVANE_1', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('COVID-19 Vakciny', 'ZASOBY_APLIKOVANE_2', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('COVID-19 Vakciny', 'VAKC_VYROBCA_POPIS')
//            ->selectColumn('COVID-19 Vakciny', 'VAKC_POPIS')
//            ->selectColumn('COVID-19 Vakciny', 'SIDZAR_KRAJ_KOD_ST', PowerBiQueryBuilder::AGGREGATION_COUNT),

//            ->andWhere('Ag testy', 'DATUM_VYPL_F', PowerBiQueryBuilder::COMPARISON_GREATER_THAN, 'datetime\'2021-01-09T00:00:00\'')
            , new DatePaginationHint('Ag testy', 'DATUM_VYPL_F', DateTimeImmutable::createFromFormat('Y-m-d', '2020-09-01'), 50)
        );
    }
}
