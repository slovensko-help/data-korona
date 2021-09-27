<?php

namespace App\Client\PowerBi;

use App\Entity\District;
use App\Entity\Raw\PowerBiAgTests;
use App\QueryBuilder\Hint\DatePaginationHint;
use App\QueryBuilder\PowerBiQueryBuilder;
use DateTimeImmutable;
use Generator;
use Symfony\Component\String\Slugger\SluggerInterface;

class AgTestsClient extends AbstractClient
{
    const REPORT_URL = 'https://app.powerbi.com/view?r=eyJrIjoiNjAzMDM0NGItYTFlZC00NTVkLWJkZjAtYzk3NTQ4NTU5MmEzIiwidCI6IjMxMGJhNTk1LTAxM2MtNDAyZC05ZWYyLWI1N2Q1ZjFkY2Q2MyIsImMiOjl9';
    const REPORT_BEGINNING = '2020-09-01';

    /** @var SluggerInterface */
    private $slugger;

    public function findAll(): Generator
    {
        yield from $this->all($this->createQueryBuilder()
            ->selectColumn('Ag testy', 'DATUM_VYPL_F')
            ->selectColumn('Ag testy', 'AGTEST_OKRES_KOD_ST')

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
            ->orderBy('Ag testy', 'DATUM_VYPL_F', PowerBiQueryBuilder::ORDER_ASC)
            , new DatePaginationHint('Ag testy', 'DATUM_VYPL_F', DateTimeImmutable::createFromFormat('Y-m-d', self::REPORT_BEGINNING), 50)
        );
    }

    public function entities(): callable
    {
        return function (array $_) {
            yield 'code:readonly' => $this->district($_);
            yield 'code' => $this->agTests($_);
        };
    }

    private function district(array $_): ?callable
    {
        return function (District $district) use ($_) {
            if (null === $_[1]) {
                return null;
            }

            return $district
                ->setCode($_[1]);
        };
    }

    private function agTests(array $_): ?callable
    {
        return function (PowerBiAgTests $agTests, ?District $district) use ($_) {
            if (null === $district) {
                return null;
            }

            $publishedOn = (new DateTimeImmutable())->setTimestamp($_[0] / 1000)->setTime(0, 0);

            return $agTests
                ->setDistrict($district)
                ->setPublishedOn($publishedOn)
                ->setNegativesCount((int)$_[2])
                ->setPositivesCount((int)$_[3])
                ->setCode($this->code($publishedOn, $district));
        };
    }

    private function code(DateTimeImmutable $publishedOn, ?District $district)
    {
        return sprintf('%s-%s',
            $publishedOn->format('Ymd'),
            str_pad(null === $district ? '0' : (string)$district->getId(), 4, '0', STR_PAD_LEFT)
        );
    }
}
