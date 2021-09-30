<?php

namespace App\Command;

use App\Client\PowerBi\VaccinatedPatientsClient;
use App\Client\PowerBi\VaccinatedTestsClient;
use App\Entity\Aggregation\SlovakiaVaccinatedPeople;
use App\Entity\Raw\PowerBiVaccinatedPatients;
use App\Entity\Raw\PowerBiVaccinatedTests;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class VaccinatedPeopleImport extends AbstractImport
{
    protected static $defaultName = 'app:import:vaccinated-people';

    /** @var VaccinatedTestsClient */
    protected $powerBiClientTests;

    /** @var VaccinatedPatientsClient */
    protected $powerBiClientPatients;
    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->disableDoctrineLogger();

        $output->writeln($this->log('Updating powerBi vaccinated tests.'));
        $this->persist(
            $this->powerBiClientTests->findAll(),
            $this->powerBiClientTests->entities(),
            [PowerBiVaccinatedTests::class => [null, null, true],]
        );
        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating powerBi vaccinated patients.'));
        $this->persist(
            $this->powerBiClientPatients->findAll(),
            $this->powerBiClientPatients->entities(),
            [PowerBiVaccinatedPatients::class => [null, null, true],]
        );
        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating slovak vaccinated people.'));
        $this->entityManager->createQuery("
                delete from App\Entity\Aggregation\SlovakiaVaccinatedPeople e 
                where e.publishedOn >= :published_on")
            ->setParameter('published_on', new DateTimeImmutable('30 days ago'), Types::DATE_IMMUTABLE)
            ->execute();

        foreach ($this->vaccinatedPeople() as $vaccinatedPeopleDay) {
            $this->entityManager->persist($vaccinatedPeopleDay);
        }

        $this->entityManager->flush();
        $output->writeln($this->log('DONE.'));

        return self::SUCCESS;
    }

    private function rawVaccinatedTests(): array
    {
        $rawTests = $this->entityManager->createQuery("
                select e from App\Entity\Raw\PowerBiVaccinatedTests e 
                where e.publishedOn >= :published_on")
            ->setParameter('published_on', new DateTimeImmutable('30 days ago'), Types::DATE_IMMUTABLE)
            ->getResult();

        $result = [];

        /** @var PowerBiVaccinatedTests $test */
        foreach ($rawTests as $test) {
            $publishedOn = $test->getPublishedOn()->format('Y-m-d');

            if (!isset($result[$publishedOn])) {
                $result[$publishedOn] = [
                    'published_on' => $test->getPublishedOn(),
                    'groups' => [
                        'ag-positive' => [
                            'fully-vaccinated' => 0,
                            'partially-vaccinated' => 0,
                            'unvaccinated' => 0,
                            'unknown' => 0,
                        ],
                        'ag-negative' => [
                            'fully-vaccinated' => 0,
                            'partially-vaccinated' => 0,
                            'unvaccinated' => 0,
                            'unknown' => 0,
                        ],
                        'pcr-positive' => [
                            'fully-vaccinated' => 0,
                            'partially-vaccinated' => 0,
                            'unvaccinated' => 0,
                            'unknown' => 0,
                        ],
                        'pcr-negative' => [
                            'fully-vaccinated' => 0,
                            'partially-vaccinated' => 0,
                            'unvaccinated' => 0,
                            'unknown' => 0,
                        ],
                    ]
                ];
            }

            $testKey = mb_strtolower($test->getTestType() . '-' . $test->getTestResult());
            $vaccinatedKey = mb_strtolower($test->getVaccinationStatus());

            $result[$publishedOn]['groups'][$testKey][$vaccinatedKey] = $test->getCount();
        }

        return $result;
    }

    private function rawVaccinatedPatients(): array
    {
        $rawPatients = $this->entityManager->createQuery("
                select e from App\Entity\Raw\PowerBiVaccinatedPatients e 
                where e.publishedOn >= :published_on")
            ->setParameter('published_on', new DateTimeImmutable('30 days ago'), Types::DATE_IMMUTABLE)
            ->getResult();

        $result = [];

        /** @var PowerBiVaccinatedPatients $patient */
        foreach ($rawPatients as $patient) {
            $publishedOn = $patient->getPublishedOn()->format('Y-m-d');

            if (!isset($result[$publishedOn])) {
                $result[$publishedOn] = [
                    'published_on' => $patient->getPublishedOn(),
                    'group' => [
                        'fullyVaccinated' => 0,
                        'partiallyVaccinated' => 0,
                        'unknownDoseButVaccinated' => 0,
                        'unvaccinated' => 0,
                        'unknown' => 0,
                    ]
                ];
            }

            if (isset($result[$publishedOn]['group'][$patient->getVaccinationStatus()])) {
                $result[$publishedOn]['group'][$patient->getVaccinationStatus()] = $patient->getCount();
            }
        }

        return $result;
    }

    private function vaccinatedPeople(): array
    {
        $vaccinatedPeople = [];

        foreach ($this->rawVaccinatedTests() as $publishedOn => $test) {
            $vaccinatedPeopleDay = new SlovakiaVaccinatedPeople();
            $vaccinatedPeopleDay->setPublishedOn($test['published_on']);
            $vaccinatedPeopleDay->setId($test['published_on']->format('Ymd'));

            foreach ($test['groups'] as $groupKey => $group) {
                $groupRatios = $this->calculateRatios($group);

                foreach ($groupRatios as $vaccinatedKey => $value) {
                    $propertyName = str_replace('-', '_', $vaccinatedKey . '-' . $groupKey . 'sRate');
                    $this->propertyAccessor->setValue($vaccinatedPeopleDay, $propertyName, $value);
                }
            }

            $vaccinatedPeople[$publishedOn] = $vaccinatedPeopleDay;
        }

        foreach ($this->rawVaccinatedPatients() as $publishedOn => $patient) {
            $vaccinatedPeopleDay = $vaccinatedPeople[$publishedOn] ?? new SlovakiaVaccinatedPeople();
            $vaccinatedPeopleDay->setPublishedOn($patient['published_on']);
            $vaccinatedPeopleDay->setId($patient['published_on']->format('Ymd'));

            $groupRatios = $this->calculateRatios($patient['group']);

            foreach ($groupRatios as $vaccinatedKey => $value) {
                $this->propertyAccessor->setValue($vaccinatedPeopleDay, $vaccinatedKey . 'PatientsRate', $value);
            }

            $vaccinatedPeople[$publishedOn] = $vaccinatedPeopleDay;
        }

        return $vaccinatedPeople;
    }

    private function calculateRatios(array $items) {
        $sum = 0;
        $result = [];

        foreach ($items as $value) {
            $sum += $value;
        }

        foreach ($items as $key => $value) {
            $result[$key] = 0 === $sum ? 0 : (int) round($value / $sum * 10000);
        }

        return $result;
    }

    /**
     * @required
     * @param VaccinatedTestsClient $powerBiClientTests
     */
    public function setPowerBiClientTests(VaccinatedTestsClient $powerBiClientTests)
    {
        $this->powerBiClientTests = $powerBiClientTests;
    }

    /**
     * @required
     * @param VaccinatedPatientsClient $powerBiClientPatients
     */
    public function setPowerBiClientPatients(VaccinatedPatientsClient $powerBiClientPatients)
    {
        $this->powerBiClientPatients = $powerBiClientPatients;
    }

    /**
     * @required
     * @param PropertyAccessorInterface $propertyAccessor
     */
    public function setPropertyAccessor(PropertyAccessorInterface $propertyAccessor)
    {
        $this->propertyAccessor = $propertyAccessor;
    }
}
