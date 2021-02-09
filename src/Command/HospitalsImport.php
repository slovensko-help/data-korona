<?php

namespace App\Command;

use App\Client\Iza\HospitalsClient as IzaHospitalsClient;
use App\Repository\Aggregation\AbstractRepository as AbstractAggregationRepository;
use App\Repository\Aggregation\DistrictHospitalBedsRepository;
use App\Repository\Aggregation\DistrictHospitalPatientsRepository;
use App\Repository\Aggregation\RegionHospitalBedsRepository;
use App\Repository\Aggregation\RegionHospitalPatientsRepository;
use App\Repository\Aggregation\SlovakiaHospitalBedsRepository;
use App\Repository\Aggregation\SlovakiaHospitalPatientsRepository;
use App\Repository\EntityRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HospitalsImport extends AbstractImport
{
    protected static $defaultName = 'app:import:hospitals';

    /** @var IzaHospitalsClient */
    protected $izaHospitalsClient;

    /**
     * @var AbstractAggregationRepository[]
     */
    private $aggregationRepositories = [];

    /**
     * @var EntityRepository[]
     */
    private $itemRepositories;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->disableDoctrineLogger();

        $output->writeln($this->log('Updating hospitals/cities/districts/regions/hospitalBeds/hospitalPatients/hospitalStaff...'));
        $this->persist(
            $this->izaHospitalsClient->findAll(),
            $this->izaHospitalsClient->entities(),
            [],
            1000
        );
        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating aggregations...'));
        $this->updateAggregations($output);
        $output->writeln($this->log('DONE.'));

        return self::SUCCESS;
    }


    private function updateAggregations(OutputInterface $output)
    {
        foreach ($this->aggregationRepositories as $repository) {
            $output->writeln($this->log('Updating ' . $repository->getClassName()));

            foreach ($repository->items() as $i => $item) {
                $this->entityManager->persist($item);

                if (($i + 1) % 1000 === 0) {
                    $repository->commitChangesToDb();
                }
            }

            $repository->commitChangesToDb();
        }
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

    /**
     * @required
     * @param IzaHospitalsClient $izaHospitalsClient
     */
    public function setIzaHospitalsClient(IzaHospitalsClient $izaHospitalsClient): void
    {
        $this->izaHospitalsClient = $izaHospitalsClient;
    }
}