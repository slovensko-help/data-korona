<?php

namespace App\Client\PowerBi;

use App\QueryBuilder\Hint\DatePaginationHint;
use App\QueryBuilder\PowerBiQueryBuilder;
use DateTimeImmutable;
use Generator;

class DebugClient extends AbstractClient
{
    const REPORT_URL = 'https://app.powerbi.com/view?r=eyJrIjoiOWNmMTMzODQtMDhkOS00YTlkLWJkODAtOGFjZThjMTE5ZmUwIiwidCI6IjMxMGJhNTk1LTAxM2MtNDAyZC05ZWYyLWI1N2Q1ZjFkY2Q2MyIsImMiOjl9';

    public function debug(): Generator
    {
        $builder = $this->createQueryBuilder();

        $arithmetic = [
            'Arithmetic' => $builder->leftRight(
                    $builder->term('PCOV_PP_M2V_DENNY_STAV', 'ID_PAC', PowerBiQueryBuilder::SELECT_TYPE_COLUMN, PowerBiQueryBuilder::AGGREGATION_COUNT),
                    [
                        'ScopedEval' => [
                            'Expression' => $builder->term('PCOV_PP_M2V_DENNY_STAV', 'ID_PAC', PowerBiQueryBuilder::SELECT_TYPE_COLUMN, PowerBiQueryBuilder::AGGREGATION_COUNT),
                            'Scope' => [
                                $builder->term('PCOV_PP_M2V_DENNY_STAV', 'P.VEK_SKUP28', PowerBiQueryBuilder::SELECT_TYPE_COLUMN)
                            ],
                        ]
                    ]
                ) + ['Operator' => 3]
        ];

        yield from $this->all($builder
//            ->selectMeasure('DatumCasAktualizacie', 'Dátum poslednej aktualizácie')
//            ->selectColumn('Ag testy', 'DATUM_VYPL_F')
//            ->selectColumn('Ag testy', 'AGTEST_OKRES_KOD_ST')
//            ->selectColumn('Ag testy', 'AGTEST_OKRES_POPIS')
//            ->selectMeasure('Ag testy', 'Negativne')
//            ->selectMeasure('Ag testy', 'Pozitivne')
//            ->selectMeasure('Ag testy', '% negat')
//            ->selectMeasure('Ag testy', '% pozit')
//            ->orderBy('Ag testy', 'DATUM_VYPL_F', PowerBiQueryBuilder::ORDER_ASC)

//            ->selectColumn('Web - Pozitivne nalezy', 'Dátum')
//            ->selectColumn('Web - Pozitivne nalezy', 'Nazov_okresu')
//            ->selectColumn('Web - Pozitivne nalezy', 'patient_sex')
//            ->selectColumn('Web - Pozitivne nalezy', 'Vekove kategorie')
//            ->selectColumn('Web - Pozitivne nalezy', 'Count', PowerBiQueryBuilder::AGGREGATION_SUM)
////            ->andWhere('Web - Pozitivne nalezy', 'Dátum', PowerBiQueryBuilder::COMPARISON_GREATER_THAN, 'datetime\'2021-06-07T00:00:00\'')
//            ->orderBy('Web - Pozitivne nalezy', 'Dátum', PowerBiQueryBuilder::ORDER_ASC)

//            ->selectColumn('PCOV_VAKCIN_TEST_PBIV', 'DAT_VYSLEDKU_TESTU')
//            ->selectColumn('PCOV_VAKCIN_TEST_PBIV', 'STAV_OCK_PBI')
//            ->selectColumn('PCOV_VAKCIN_TEST_PBIV', 'TYP_TESTU')
//            ->selectColumn('PCOV_VAKCIN_TEST_PBIV', 'VYSLEDOK_TESTU')
////            ->selectColumn('PCOV_VAKCIN_TEST_PBIV', 'Timestamp_text')
//            ->selectColumn('PCOV_VAKCIN_TEST_PBIV', 'POCET_TESTOV', PowerBiQueryBuilder::AGGREGATION_SUM)
////            ->andWhere('PCOV_VAKCIN_TEST_PBIV', 'TYP_TESTU', PowerBiQueryBuilder::COMPARISON_EQUAL, '\'AG\'')
////            ->andWhere('PCOV_VAKCIN_TEST_PBIV', 'VYSLEDOK_TESTU', PowerBiQueryBuilder::COMPARISON_EQUAL, '\'POSITIVE\'')
//            ->andWhere('PCOV_VAKCIN_TEST_PBIV', 'DAT_VYSLEDKU_TESTU', PowerBiQueryBuilder::COMPARISON_GREATER_THAN_OR_EQUAL, 'datetime\'2021-08-01T00:00:00\'')


            // zaockovanost u hospitalizovanych - percenta a cisla
//            ->selectMeasure('PCOV_PP_M2V_DENNY_STAV', 'OCKOVANI %')
//            ->selectMeasure('PCOV_PP_M2V_DENNY_STAV', 'NEOCKOVANI %')
//            ->selectColumn('PCOV_PP_M2V_DENNY_STAV', 'P.VAKCINACIA_POPIS')
//            ->selectColumn('PCOV_PP_M2V_DENNY_STAV', 'P.VAKCINA_TYP')
//            ->selectColumn('PCOV_PP_M2V_DENNY_STAV', 'P.VEK_SKUP30')
//            ->selectColumn('PCOV_PP_M2V_DENNY_STAV', 'P.VEK_SKUP_PBI')
//            ->selectColumn('PCOV_PP_M2V_DENNY_STAV', 'P.VEK_SKUP13')
//            ->selectMeasure('PCOV_PP_M2V_DENNY_STAV', 'ID_PAC_POCET')
//            ->selectColumn('PCOV_PP_M2V_DENNY_STAV', 'P.PACKRAJ_ST')
//            ->selectColumn('PCOV_PP_M2V_DENNY_STAV', 'P.VEK_SKUP28')
//            ->selectArithmetic('percento', $arithmetic)
//            ->selectColumn('PCOV_PP_M2V_DENNY_STAV', 'ID_PAC', PowerBiQueryBuilder::AGGREGATION_COUNT_NOT_NULL)
//            ->andWhere('PCOV_PP_M2V_DENNY_STAV', 'P.VEK_SKUP28', PowerBiQueryBuilder::COMPARISON_EQUAL, '\'65+\'')
//            ->andWhere('PCOV_PP_M2V_DENNY_STAV', 'P.VAKCINACIA_POPIS', PowerBiQueryBuilder::COMPARISON_EQUAL, '\'nie\'')

//            ->orderBy('PCOV_PP_M2V_DENNY_STAV', 'P.VAKCINACIA_POPIS', PowerBiQueryBuilder::ORDER_ASC)
//            ->orderBy('PCOV_PP_M2V_DENNY_STAV', 'P.VEK_SKUP28', PowerBiQueryBuilder::ORDER_ASC)

        // zaockovani hospitalizovani podla davky - percenta a cisla
//            ->selectMeasure('PCOV_PP_M2V_DENNY_STAV', 'DAVKA_1 %')
//            ->selectMeasure('PCOV_PP_M2V_DENNY_STAV', 'DAVKA_2 %')
//            ->selectMeasure('PCOV_PP_M2V_DENNY_STAV', 'NEVYPLNENE %')
//            ->selectMeasure('PCOV_PP_M2V_DENNY_STAV', 'DAVKA_1')
//            ->selectMeasure('PCOV_PP_M2V_DENNY_STAV', 'DAVKA_2')

            ->selectMeasure('DatumCasAktualizacie', 'Dátum poslednej aktualizácie')
            ->selectColumn('PCOV_PP_M2V_DENNY_STAV', 'P.VAKCINACIA_POPIS')
            ->selectMeasure('PCOV_PP_M2V_DENNY_STAV', 'DAVKA_1')
            ->selectMeasure('PCOV_PP_M2V_DENNY_STAV', 'DAVKA_2')
            ->selectMeasure('PCOV_PP_M2V_DENNY_STAV', 'NEVYPLNENA_VAKCINA_OCK')
            ->selectColumn('PCOV_PP_M2V_DENNY_STAV', 'ID_PAC', PowerBiQueryBuilder::AGGREGATION_COUNT_NOT_NULL)

//            ->selectColumn('Ag testy', 'AGTEST_KRAJ_POPIS')

//            ->selectColumn('Ag testy', 'ANTG_TESTY_VYSLEDOK')
//            ->selectColumn('Ag testy', 'ANTG_TESTY_POCET', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_ZP', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_VEREJNOST', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_PLOSNE', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_PACIENTI', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_NZP', PowerBiQueryBuilder::AGGREGATION_SUM)
//            ->selectColumn('Ag testy', 'ANTG_TESTY_INIOKRUH', PowerBiQueryBuilder::AGGREGATION_SUM)



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
//            , new DatePaginationHint('PCOV_VAKCIN_TEST_PBIV', 'DAT_VYSLEDKU_TESTU', DateTimeImmutable::createFromFormat('Y-m-d', '2021-09-01'), 50)
        );
    }
}
