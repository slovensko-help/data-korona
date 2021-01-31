<?php

namespace App\Client\PowerBi;

use App\QueryBuilder\PowerBiQueryBuilder;

class DemoClient extends AbstractClient
{
    const REPORT_URL = 'https://app.powerbi.com//view?r=eyJrIjoiMzk4ZmRjNmEtYmZiNC00NGRiLWE2NDEtNWRjNjFhMDM4Nzk1IiwidCI6IjMxMGJhNTk1LTAxM2MtNDAyZC05ZWYyLWI1N2Q1ZjFkY2Q2MyIsImMiOjl9';

    public function findAll()
    {
        return $this->execute($this->createQueryBuilder()
            ->selectColumn('Ag testy', 'DATUM_VYPL_F')
            ->selectColumn('Ag testy', 'AGTEST_OKRES_POPIS')
//            ->selectColumn('Ag testy', 'ANTG_TESTY_POCET')
            ->selectColumn('Ag testy', 'ANTG_TESTY_VYSLEDOK')
            ->selectColumn('Ag testy', 'ANTG_TESTY_POCET', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_ZP', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_VEREJNOST', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_PLOSNE', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_PACIENTI', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_NZP', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_INIOKRUH', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'Karanténa')
//            ->orderBy('Ag testy', 'ANTG_TESTY_POCET', PowerBiQueryBuilder::ORDER_DESC)
//            ->andWhere('Ag testy', 'DATUM_VYPL_F', PowerBiQueryBuilder::COMPARISON_GREATER_THAN_OR_EQUAL, 'datetime\'2020-12-01T00:00:00\'')
            ->andWhere('Ag testy', 'DATUM_VYPL_F', PowerBiQueryBuilder::COMPARISON_GREATER_THAN_OR_EQUAL, 'datetime\'2021-01-30T00:00:00\'')
//            ->andWhere('Ag testy', 'AGTEST_OKRES_POPIS', PowerBiQueryBuilder::COMPARISON_EQUAL, '\'Okres Nitra\'')
//            ->andWhere('Ag testy', 'ANTG_TEST_VYSLEDOK_P', PowerBiQueryBuilder::COMPARISON_EQUAL, '\'pozitívny\'')

//            ->selectColumn('Web - Pozitivne nalezy', 'Karanténa')
//            ->selectMeasure('Web - Pozitivne nalezy', 'LastDayPozit')
//            ->selectColumn('Umrtia', 'DATUM_ZAR')
//            ->selectColumn('Umrtia', 'POC_VYLIECENI', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('COVID-19 Nemocnice Hist', 'SIDOU_OKRES_POP_ST')
//            ->selectColumn('COVID-19 Vakciny', 'DATUM_VYPL')
////            ->selectColumn('COVID-19 Pocet obyvatelov', 'KRAJ_POPIS')
//////            ->selectColumn('COVID-19 Pocet obyvatelov', 'KRAJ_POPIS')
////            ->selectColumn('COVID-19 Vakciny', 'VAKC_VYROBCA_POPIS')
//            ->selectColumn('COVID-19 Vakciny', 'ZASOBY_APLIKOVANE_1', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('COVID-19 Vakciny', 'ZASOBY_APLIKOVANE_2', PowerBiQueryBuilder::AGGREGATION_SUM)
////            ->selectColumn('COVID-19 Vakciny', 'VAKC_POPIS')
//            ->andWhere('COVID-19 Vakciny', 'DATUM_VYPL', PowerBiQueryBuilder::COMPARISON_GREATER_THAN_OR_EQUAL, 'datetime\'2021-01-21T00:00:00\'')

//            ->selectColumn('COVID-19 Pocet obyvatelov', 'KRAJ_POPIS')
//            ->selectColumn('COVID-19 Pocet obyvatelov', 'POCET_OBYV')

//            ->selectColumn('Web - Pozitivne nalezy', 'Dátum')
//            ->selectColumn('Web - Pozitivne nalezy', 'Count', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Web - Pozitivne nalezy', 'Vekove kategorie')
//            ->selectColumn('Web - Pozitivne nalezy', 'Nazov_okresu')
//            ->selectColumn('Web - Pozitivne nalezy', 'patient_sex')
////            ->andWhere('Web - Pozitivne nalezy', 'Dátum', PowerBiQueryBuilder::COMPARISON_GREATER_THAN, 'datetime\'2021-01-25T00:00:00\'')
//            ->andWhere('Web - Pozitivne nalezy', 'Dátum', PowerBiQueryBuilder::COMPARISON_GREATER_THAN_OR_EQUAL, 'datetime\'2021-01-28T00:00:00\'')

//            ->orderBy('COVID-19 Pocet obyvatelov', 'KRAJ_POPIS', PowerBiQueryBuilder::ORDER_DESC)
        )->items();
    }
}