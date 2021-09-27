<?php

namespace App\Client\PowerBi;

use App\Entity\Raw\PowerBiVaccinatedPatients;
use App\Entity\Raw\PowerBiVaccinatedTests;
use App\QueryBuilder\Hint\DatePaginationHint;
use App\QueryBuilder\PowerBiQueryBuilder;
use DateInterval;
use DateTimeImmutable;
use Generator;
use Symfony\Component\String\Slugger\SluggerInterface;

class VaccinatedPatientsClient extends AbstractClient
{
    const REPORT_URL = 'https://app.powerbi.com/view?r=eyJrIjoiOWNmMTMzODQtMDhkOS00YTlkLWJkODAtOGFjZThjMTE5ZmUwIiwidCI6IjMxMGJhNTk1LTAxM2MtNDAyZC05ZWYyLWI1N2Q1ZjFkY2Q2MyIsImMiOjl9';

    /** @var SluggerInterface */
    private $slugger;

    public function findAll(): Generator
    {
        yield from $this->all($this->createQueryBuilder()
            ->selectMeasure('DatumCasAktualizacie', 'Dátum poslednej aktualizácie')
            ->selectColumn('PCOV_PP_M2V_DENNY_STAV', 'P.VAKCINACIA_POPIS')
            ->selectColumn('PCOV_PP_M2V_DENNY_STAV', 'ID_PAC', PowerBiQueryBuilder::AGGREGATION_COUNT_NOT_NULL)
        );
    }

    public function entities(): callable
    {
        return function (array $_) {
            yield 'id' => $this->vaccinatedPatients($_);
        };
    }

    private function vaccinatedPatients(array $_): ?callable
    {
        return function (PowerBiVaccinatedPatients $agTests) use ($_) {
            $publishedOn = (new DateTimeImmutable())->setTimestamp($_[0] / 1000)->setTime(0, 0)->sub(new DateInterval('P1D'));
            $vaccinationStatus = $this->vaccinationStatus($_[1]);

            return $agTests
                ->setId($this->id($publishedOn, $vaccinationStatus, $_))
                ->setPublishedOn($publishedOn)
                ->setVaccinationStatus($vaccinationStatus)
                ->setCount((int)$_[2]);
        };
    }

    private function vaccinationStatus(string $rawValue): string {
        $value = $this->slugger
            ->slug($rawValue, '-')
            ->lower();

        if (mb_strpos($value, 'ano') !== false) {
            return 'vaccinated';
        }

        if (mb_strpos($value, 'nie') !== false) {
            return 'unvaccinated';
        }

        return 'unknown';
    }

    private function id(DateTimeImmutable $publishedOn, string $vaccinationStatus, array $_): string {
        return $publishedOn->format('Ymd') . '-' . $vaccinationStatus;
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
