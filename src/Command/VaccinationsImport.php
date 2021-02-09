<?php

namespace App\Command;

use App\Client\Iza\VaccinationsClient as IzaVaccinationsClient;
use App\Client\Nczi\VaccinationsClient as NcziVaccinationsClient;
use App\Client\PowerBi\VaccinationsClient as PowerBiVaccinationsClient;
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
        $this->addOption('nczi-only');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->disableDoctrineLogger();

        if ($input->getOption('dump-powerbi-schema')) {
            $this->dumpPowerBiSchema($this->powerBiClient, $output);
            return self::SUCCESS;
        }

        $output->writeln($this->log('Updating powerBi/NCZI/IZA vaccinactions.'));
        $this->persist($this->powerBiClient->findAllByRegionAndVaccine(), $this->powerBiClient->entitiesByRegionAndVaccine());
//        $this->persist($this->powerBiClient->findAllByRegion(), $this->powerBiClient->entitiesByRegion());
        $this->persist($this->ncziClient->findAll(), $this->ncziClient->entities());
        $this->persist($this->izaClient->findAll(), $this->izaClient->entities());
        $output->writeln($this->log('DONE.'));

        $vaccinationsRepository = $this->entityManager->getRepository(Vaccinations::class);

        $output->writeln($this->log('Updating Vaccinactions.'));
//        $this->persist($vaccinationsRepository->vaccinationsFromRawEntities(), $vaccinationsRepository->vaccinationsEntities());
//        $this->persist($vaccinationsRepository->slovakiaVaccinations(), $vaccinationsRepository->slovakiaVaccinationsEntities());
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