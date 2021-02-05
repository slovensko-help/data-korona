<?php

namespace App\Client\PowerBi;

use App\Entity\Raw\PowerBiVaccinations;
use App\Entity\Region;
use App\Entity\Vaccine;
use App\QueryBuilder\Hint\DatePaginationHint;
use App\QueryBuilder\PowerBiQueryBuilder;
use App\Tool\Id;
use DateTimeImmutable;
use Generator;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\String\UnicodeString;

class VaccinationsClient extends AbstractClient
{
    const REPORT_URL = 'https://app.powerbi.com//view?r=eyJrIjoiMzk4ZmRjNmEtYmZiNC00NGRiLWE2NDEtNWRjNjFhMDM4Nzk1IiwidCI6IjMxMGJhNTk1LTAxM2MtNDAyZC05ZWYyLWI1N2Q1ZjFkY2Q2MyIsImMiOjl9';

    /** @var SluggerInterface */
    private $slugger;

    public function dump(): Generator
    {
        yield from $this->all($this->createQueryBuilder()
            ->selectColumn('COVID-19 Vakciny', 'DATUM_VYPL')
            ->selectColumn('COVID-19 Vakciny', 'SIDZAR_KRAJ_KOD_ST')
            ->selectColumn('COVID-19 Vakciny', 'ZASOBY_APLIKOVANE_1', PowerBiQueryBuilder::AGGREGATION_SUM)
            ->selectColumn('COVID-19 Vakciny', 'ZASOBY_APLIKOVANE_2', PowerBiQueryBuilder::AGGREGATION_SUM)
            ->selectColumn('COVID-19 Vakciny', 'VAKC_VYROBCA_POPIS')
            ->selectColumn('COVID-19 Vakciny', 'VAKC_POPIS')
            ->selectColumn('COVID-19 Vakciny', 'SIDZAR_KRAJ_KOD_ST', PowerBiQueryBuilder::AGGREGATION_COUNT)
            ,
            new DatePaginationHint('COVID-19 Vakciny', 'DATUM_VYPL', DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01'), 60)
        );
    }

    public function findAllByRegion(): Generator
    {
        yield from $this->dataItems($this->all($this->createQueryBuilder()
            ->selectColumn('COVID-19 Vakciny', 'DATUM_VYPL')
            ->selectColumn('COVID-19 Vakciny', 'SIDZAR_KRAJ_KOD_ST')
            ->selectColumn('COVID-19 Vakciny', 'ZASOBY_APLIKOVANE_1', PowerBiQueryBuilder::AGGREGATION_SUM)
            ->selectColumn('COVID-19 Vakciny', 'ZASOBY_APLIKOVANE_2', PowerBiQueryBuilder::AGGREGATION_SUM)
            ->selectColumn('COVID-19 Vakciny', 'VAKC_VYROBCA_POPIS')
            ->selectColumn('COVID-19 Vakciny', 'VAKC_POPIS')
            ->selectColumn('COVID-19 Vakciny', 'SIDZAR_KRAJ_KOD_ST', PowerBiQueryBuilder::AGGREGATION_COUNT),
            new DatePaginationHint('COVID-19 Vakciny', 'DATUM_VYPL', DateTimeImmutable::createFromFormat('Y-m-d', '2021-01-01'), 60)
        ));
    }

    protected function dataItemToEntities(array $dataItem): array
    {
        return [
            [Region::class, 'code', $this->region($dataItem)],
            [Vaccine::class, 'code', $this->vaccine($dataItem)],
            [PowerBiVaccinations::class, 'code', $this->vaccinations($dataItem)],
        ];
    }

    private function vaccine(array $_): callable
    {
        return function (Vaccine $vaccine) use ($_) {
            return $vaccine
                ->setCode($this->vaccineId($_[4], $_[5]))
                ->setManufacturer($_[4])
                ->setTitle($_[5]);
        };
    }

    private function vaccineId(string $vaccineManufacturer, string $vaccineTitle): string
    {
        return $this->slugger
            ->slug($vaccineManufacturer . ' ' . $vaccineTitle, '')
            ->lower()
            ->replaceMatches('/[aeiouy]/', '')
            ->slice(0, 100);
    }

    private function vaccinationsCode(DateTimeImmutable $publishedOn, Region $region, Vaccine $vaccine)
    {
        return sprintf('%s-%s-%s', $publishedOn->format('Ymd'), $region->getId(), $vaccine->getId());
    }

    private function vaccinations(array $_): callable
    {
        return function (PowerBiVaccinations $vaccinations, Region $region, Vaccine $vaccine) use ($_) {
            $publishedOn = (new DateTimeImmutable())->setTimestamp($_[0] / 1000)->setTime(0, 0);

            return $vaccinations
                ->setCode($this->vaccinationsCode($publishedOn, $region, $vaccine))
                ->setPublishedOn($publishedOn)
                ->setRegion($region)
                ->setVaccine($vaccine)
                ->setDose1Count($this->nullOrInt($_[2]))
                ->setDose2Count($this->nullOrInt($_[3]));
        };
    }

    private function region(array $_): ?callable
    {
        if ($this->isInvalidCode($_[1])) {
            return null;
        }

        return function (Region $region) use ($_) {
            return $region
                ->setCode($_[1]);
        };
    }

    /**
     * @required
     * @param SluggerInterface $slugger
     */
    public function setSlugger(SluggerInterface $slugger): void
    {
        $this->slugger = $slugger;
    }
}