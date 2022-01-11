<?php

namespace App\Client\PowerBi;

use App\Entity\Raw\PowerBiVaccinations;
use App\Entity\Region;
use App\Entity\Vaccine;
use App\QueryBuilder\Hint\DatePaginationHint;
use App\QueryBuilder\PowerBiQueryBuilder;
use DateTimeImmutable;
use Generator;
use Symfony\Component\String\Slugger\SluggerInterface;

class VaccinationsClient extends AbstractClient
{
    const REPORT_URL = 'https://app.powerbi.com/view?r=eyJrIjoiMzk4ZmRjNmEtYmZiNC00NGRiLWE2NDEtNWRjNjFhMDM4Nzk1IiwidCI6IjMxMGJhNTk1LTAxM2MtNDAyZC05ZWYyLWI1N2Q1ZjFkY2Q2MyIsImMiOjl9';
    const REPORT_BEGINNING = '2021-01-01';

    /** @var SluggerInterface */
    private $slugger;

    public function findAllByRegionAndVaccine(): Generator
    {
        yield from $this->all($this->createQueryBuilder()
            ->selectColumn('COVID-19 Vakciny', 'DATUM_VYPL')
            ->selectColumn('COVID-19 Vakciny', 'SIDZAR_KRAJ_KOD_ST')
            ->selectColumn('COVID-19 Vakciny', 'ZASOBY_APLIKOVANE_1', PowerBiQueryBuilder::AGGREGATION_SUM)
            ->selectColumn('COVID-19 Vakciny', 'ZASOBY_APLIKOVANE_2', PowerBiQueryBuilder::AGGREGATION_SUM)
            ->selectColumn('COVID-19 Vakciny', 'VAKC_VYROBCA_POPIS')
            ->selectColumn('COVID-19 Vakciny', 'VAKC_POPIS')
            ->selectColumn('COVID-19 Vakciny', 'SIDZAR_KRAJ_KOD_ST', PowerBiQueryBuilder::AGGREGATION_COUNT),
            new DatePaginationHint('COVID-19 Vakciny', 'DATUM_VYPL', DateTimeImmutable::createFromFormat('Y-m-d', self::REPORT_BEGINNING), 60)
        );
    }

    public function entitiesByRegionAndVaccine(): callable
    {
        return function (array $_) {
            yield 'code' => $this->region($_);
            yield 'code' => $this->vaccine($_);
            yield 'code' => $this->vaccinationsByRegionAndVaccine($_);
        };
    }

    private function region(array $_): ?callable
    {
        return function (Region $region) use ($_) {
            if ($this->isInvalidCode($_[1])) {
                return null;
            }

            return $region
                ->setCode($_[1]);
        };
    }

    private function vaccine(array $_): callable
    {
        return function (Vaccine $vaccine) use ($_) {
            $vaccineManufacturer = $_[4] ?? '-';
            return $vaccine
                ->setCode($this->vaccineId($vaccineManufacturer, $_[5]))
                ->setManufacturer($vaccineManufacturer)
                ->setTitle($_[5]);
        };
    }

    private function vaccinationsByRegionAndVaccine(array $_): callable
    {
        return function (PowerBiVaccinations $vaccinations, Region $region, Vaccine $vaccine) use ($_) {
            $publishedOn = (new DateTimeImmutable())->setTimestamp($_[0] / 1000)->setTime(0, 0);
            return $vaccinations
                ->setCode($this->code($publishedOn, $region, $vaccine))
                ->setPublishedOn($publishedOn)
                ->setRegion($region)
                ->setVaccine($vaccine)
                ->setDose1Count($this->nullOrInt($_[2]))
                ->setDose2Count($this->nullOrInt($_[3]));
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

    private function code(DateTimeImmutable $publishedOn, Region $region, Vaccine $vaccine)
    {
        return sprintf('%s-%s-%s',
            $publishedOn->format('Ymd'),
            str_pad((string)$region->getId(), 4, '0', STR_PAD_LEFT),
            str_pad((string)$vaccine->getId(), 4, '0', STR_PAD_LEFT)
        );
    }

    private function vaccinationsCodeByRegion(DateTimeImmutable $publishedOn, Region $region)
    {
        return sprintf('%s-%s', $publishedOn->format('Ymd'), $region->getId());
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
