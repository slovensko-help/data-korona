<?php

namespace App\Command;

use App\Repository\AbstractRepository;
use App\Repository\Aggregation\AbstractRepository as AbstractAggregationRepository;
use App\Repository\Aggregation\DistrictHospitalBedsRepository;
use App\Repository\Aggregation\DistrictHospitalPatientsRepository;
use App\Repository\Aggregation\RegionHospitalBedsRepository;
use App\Repository\Aggregation\RegionHospitalPatientsRepository;
use App\Repository\Aggregation\SlovakiaHospitalBedsRepository;
use App\Repository\Aggregation\SlovakiaHospitalPatientsRepository;
use App\Repository\HospitalBedsRepository;
use App\Repository\HospitalPatientsRepository;
use App\Repository\HospitalStaffRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HospitalsImport extends AbstractImport
{
    const CSV_FILE = 'https://raw.githubusercontent.com/Institut-Zdravotnych-Analyz/covid19-data/main/OpenData_Slovakia_Covid_Hospital_Full.csv';

    protected static $defaultName = 'app:import:hospitals';

    /**
     * @var AbstractAggregationRepository[]
     */
    private $aggregationRepositories = [];

    /**
     * @var AbstractRepository[]
     */
    private $itemRepositories;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->entityManager->getConnection()->getConfiguration()->setSQLLogger();

        $output->writeln($this->log('Downloading CSV file...'));
        $csv = $this->content->load(self::CSV_FILE);
        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating hospitals/cities/districts/regions...'));
        $this->updateStructures($this->csvRecords($csv));
        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating hospitalBeds, hospitalPatients and hospitalStaff...'));
        $this->updateItems($this->csvRecords($csv));
        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating aggregations...'));
        $this->updateAggregations($output);
        $output->writeln($this->log('DONE.'));

        return self::SUCCESS;
    }

    private function updateStructures(iterable $items)
    {
        foreach ($items as $item) {
            $this->hospital(
                $item,
                $this->city(
                    $item,
                    $this->district(
                        $item,
                        $this->region($item)
                    )
                )
            );
        }

        $this->commitChangesToDb();
    }

    private function updateItems(iterable $items)
    {
        $hospitals = $this->hospitalRepository->findAllIndexedByCode();

        foreach ($items as $i => $item) {
            $hospital = $hospitals[$item['KODPZS']] ?? null;

            foreach ($this->itemRepositories as $itemRepository) {
                $itemRepository->save($item, $hospital);

                if (($i + 1) % 200 === 0) {
                    $this->commitChangesToDb();
                    $hospitals = $this->hospitalRepository->findAllIndexedByCode();
                }
            }
        }

        $this->commitChangesToDb();
    }

    private function updateAggregations(OutputInterface $output)
    {
        foreach ($this->aggregationRepositories as $repository) {
            $output->writeln($this->log('Updating ' . $repository->getClassName()));

            foreach ($repository->items() as $i => $item) {
                $this->entityManager->persist($item);

                if (($i + 1) % 200 === 0) {
                    $repository->commitChangesToDb();
                }
            }

            $repository->commitChangesToDb();
        }
    }

    /**
     * @required
     * @param HospitalBedsRepository $hospitalBedsRepository
     * @param HospitalPatientsRepository $hospitalPatientsRepository
     * @param HospitalStaffRepository $hospitalStaffRepository
     */
    public function setItemRepositories(
        HospitalBedsRepository $hospitalBedsRepository,
        HospitalPatientsRepository $hospitalPatientsRepository,
        HospitalStaffRepository $hospitalStaffRepository
    )
    {
        $this->itemRepositories = [
            $hospitalBedsRepository,
            $hospitalPatientsRepository,
            $hospitalStaffRepository,
        ];
    }

    /**
     * @required
     * @param DistrictHospitalBedsRepository $districtHospitalBedsRepository
     * @param DistrictHospitalPatientsRepository $districtHospitalPatientsRepository
     * @param RegionHospitalBedsRepository $regionHospitalBedsRepository
     * @param RegionHospitalPatientsRepository $regionHospitalPatientsRepository
     * @param SlovakiaHospitalBedsRepository $slovakiaHospitalBedsRepository
     * @param SlovakiaHospitalPatientsRepository $slovakiaHospitalPatientsRepository
     */
    public function setAggregationRepositories(
        DistrictHospitalBedsRepository $districtHospitalBedsRepository,
        DistrictHospitalPatientsRepository $districtHospitalPatientsRepository,
        RegionHospitalBedsRepository $regionHospitalBedsRepository,
        RegionHospitalPatientsRepository $regionHospitalPatientsRepository,
        SlovakiaHospitalBedsRepository $slovakiaHospitalBedsRepository,
        SlovakiaHospitalPatientsRepository $slovakiaHospitalPatientsRepository
    )
    {
        $this->aggregationRepositories = [
            $districtHospitalBedsRepository,
            $districtHospitalPatientsRepository,
            $regionHospitalBedsRepository,
            $regionHospitalPatientsRepository,
            $slovakiaHospitalBedsRepository,
            $slovakiaHospitalPatientsRepository,
        ];
    }
}