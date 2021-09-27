<?php

namespace App\Client\PowerBi;

use App\Entity\Raw\PowerBiVaccinatedTests;
use App\QueryBuilder\Hint\DatePaginationHint;
use App\QueryBuilder\PowerBiQueryBuilder;
use DateInterval;
use DateTimeImmutable;
use Generator;
use Symfony\Component\String\Slugger\SluggerInterface;

class VaccinatedTestsClient extends AbstractClient
{
    const REPORT_URL = 'https://app.powerbi.com/view?r=eyJrIjoiOWNmMTMzODQtMDhkOS00YTlkLWJkODAtOGFjZThjMTE5ZmUwIiwidCI6IjMxMGJhNTk1LTAxM2MtNDAyZC05ZWYyLWI1N2Q1ZjFkY2Q2MyIsImMiOjl9';
    const REPORT_BEGINNING = '2021-09-01';

    /** @var SluggerInterface */
    private $slugger;

    public function findAll(): Generator
    {
        $reportBegining = (new DateTimeImmutable())->sub(new DateInterval('P30D'));

        yield from $this->all($this->createQueryBuilder()
            ->selectColumn('PCOV_VAKCIN_TEST_PBIV', 'DAT_VYSLEDKU_TESTU')
            ->selectColumn('PCOV_VAKCIN_TEST_PBIV', 'STAV_OCK_PBI')
            ->selectColumn('PCOV_VAKCIN_TEST_PBIV', 'TYP_TESTU')
            ->selectColumn('PCOV_VAKCIN_TEST_PBIV', 'VYSLEDOK_TESTU')
            ->selectColumn('PCOV_VAKCIN_TEST_PBIV', 'POCET_TESTOV', PowerBiQueryBuilder::AGGREGATION_SUM)
            ->andWhere('PCOV_VAKCIN_TEST_PBIV', 'DAT_VYSLEDKU_TESTU', PowerBiQueryBuilder::COMPARISON_GREATER_THAN_OR_EQUAL, 'datetime\'' . $reportBegining->format('Y-m-d') . 'T00:00:00\'')
            , new DatePaginationHint('PCOV_VAKCIN_TEST_PBIV', 'DAT_VYSLEDKU_TESTU', $reportBegining, 50)
        );
    }

    public function entities(): callable
    {
        return function (array $_) {
            yield 'id' => $this->vaccinatedTests($_);
        };
    }

    private function vaccinatedTests(array $_): ?callable
    {
        return function (PowerBiVaccinatedTests $agTests) use ($_) {
            $publishedOn = (new DateTimeImmutable())->setTimestamp($_[0] / 1000)->setTime(0, 0);
            $vaccinationStatus = $this->vaccinationStatus($_[1]);

            return $agTests
                ->setId($this->id($publishedOn, $vaccinationStatus, $_))
                ->setPublishedOn($publishedOn)
                ->setTestType($_[2])
                ->setTestResult($_[3])
                ->setCount((int)$_[4])
                ->setVaccinationStatus($vaccinationStatus);
        };
    }

    private function vaccinationStatus(string $rawValue): string {
        $value = $this->slugger
            ->slug($rawValue, '-')
            ->lower();

        if (mb_strpos($value, 'plne-zaockovani') !== false) {
            return 'fully-vaccinated';
        }

        if (mb_strpos($value, 'po-1-davke') !== false) {
            return 'partially-vaccinated';
        }

        if (mb_strpos($value, 'neockovani') !== false) {
            return 'unvaccinated';
        }

        return 'unknown';
    }

    private function id(DateTimeImmutable $publishedOn, string $vaccinationStatus, array $_): string {
        return $publishedOn->format('Ymd') . '-' .
            $this->slugger
                ->slug($vaccinationStatus . '-' . $_[2] . '-' . $_[3], '-')
                ->lower();
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
