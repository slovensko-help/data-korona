<?php

namespace App\Command;

use App\Client\Iza\VaccinationsClient as IzaVaccinationsClient;
use App\Client\Nczi\VaccinationsClient as NcziVaccinationsClient;
use App\Client\PowerBi\VaccinationsClient as PowerBiVaccinationsClient;
use App\Entity\Aggregation\RegionVaccinations;
use App\Entity\Aggregation\SlovakiaVaccinations;
use App\Entity\Raw\IzaVaccinations;
use App\Entity\Raw\NcziVaccinations;
use App\Entity\Raw\PowerBiVaccinations;
use App\Entity\TimeSeries\Vaccinations;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VaccinationsImport extends AbstractImport
{
    const CSV_FILE = 'https://raw.githubusercontent.com/Institut-Zdravotnych-Analyz/covid19-data/main/OpenData_Slovakia_Vaccinations.csv';

    protected static $defaultName = 'app:import:vaccinations';

    /** @var PowerBiVaccinationsClient */
    protected $powerBiClient;

    /** @var NcziVaccinationsClient */
    protected $ncziClient;

    /** @var IzaVaccinationsClient */
    protected $izaClient;

    protected function configure()
    {
        parent::configure();

        $this->addOption('dump-powerbi-schema');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->disableDoctrineLogger();

        if ($input->getOption('dump-powerbi-schema')) {
            $this->dumpPowerBiSchema($this->powerBiClient, $output);
            return self::SUCCESS;
        }

        $output->writeln($this->log('Updating powerBi/NCZI/IZA vaccinactions.'));
        $this->persist(
            $this->powerBiClient->findAllByRegionAndVaccine(),
            $this->powerBiClient->entitiesByRegionAndVaccine(),
            [PowerBiVaccinations::class => [null, null],]
        );
        $this->persist(
            $this->ncziClient->findAll(),
            $this->ncziClient->entities(),
            [NcziVaccinations::class => [null, null],]
        );
        $this->persist(
            $this->izaClient->findAll(),
            $this->izaClient->entities(),
            [IzaVaccinations::class => [null, null],]);
        $output->writeln($this->log('DONE.'));

        $vaccinationsRepository = $this->entityManager->getRepository(Vaccinations::class);

        $output->writeln($this->log('Updating Vaccinactions.'));
        // slovakiaVaccinations must be imported first because regionVaccinations and vaccinations depend on it
        $this->persist(
            $vaccinationsRepository->slovakiaVaccinations(),
            $vaccinationsRepository->slovakiaVaccinationsEntities()
        );
        $this->persist(
            $vaccinationsRepository->regionVaccinations(),
            $vaccinationsRepository->regionVaccinationsEntities()
        );
        $this->persist(
            $vaccinationsRepository->vaccinationsFromRawEntities(),
            $vaccinationsRepository->vaccinationsEntities(),
            [Vaccinations::class => [null, null],]
        );
        $output->writeln($this->log('DONE.'));

        return self::SUCCESS;
    }

    /**
     * @required
     * @param NcziVaccinationsClient $ncziClient
     */
    public function setNcziClient(NcziVaccinationsClient $ncziClient)
    {
        $this->ncziClient = $ncziClient;
    }

    /**
     * @required
     * @param IzaVaccinationsClient $izaClient
     */
    public function setIzaClient(IzaVaccinationsClient $izaClient)
    {
        $this->izaClient = $izaClient;
    }

    /**
     * @required
     * @param PowerBiVaccinationsClient $powerBiClient
     */
    public function setPowerBiClient(PowerBiVaccinationsClient $powerBiClient)
    {
        $this->powerBiClient = $powerBiClient;
    }
}