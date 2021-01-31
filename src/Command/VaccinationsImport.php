<?php

namespace App\Command;

use App\Client\Iza\VaccinationsClient as IzaVaccinationsClient;
use App\Client\Nczi\VaccinationsClient as NcziVaccinationsClient;
use App\Client\PowerBi\VaccinationsClient as PowerBiVaccinationsClient;
use App\Repository\Raw\SlovakiaNcziVaccinationsRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VaccinationsImport extends AbstractImport
{
    const CSV_FILE = 'https://raw.githubusercontent.com/Institut-Zdravotnych-Analyz/covid19-data/main/OpenData_Slovakia_Vaccinations.csv';

    protected static $defaultName = 'app:import:vaccinations';

    /** @var PowerBiVaccinationsClient */
    protected $powerBiVaccinationsClient;

    /** @var NcziVaccinationsClient */
    protected $ncziVaccinationsClient;

    /** @var IzaVaccinationsClient */
    protected $izaVaccinationsClient;

    /** @var SlovakiaNcziVaccinationsRepository */
    protected $slovakiaNcziVaccinationsRepository;

    protected function configure()
    {
        parent::configure();

        $this->addOption('dump-powerbi-schema');
        $this->addOption('nczi-only');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('dump-powerbi-schema')) {
            $this->dumpPowerBiSchema($this->powerBiVaccinationsClient, $output);
            return self::SUCCESS;
        }

        if ($input->getOption('nczi-only')) {
            $this->slovakiaNcziVaccinationsRepository->saveAll($this->ncziVaccinationsClient->findAll());
        }

        $this->commitChangesToDb();

//        dump(iterator_to_array($this->powerBiVaccinationsClient->findAllByRegion()));die;
//        dump(iterator_to_array($this->ncziVaccinationsClient->findAll()));
//        dump(iterator_to_array($this->izaVaccinationsClient->findAll()));

        return self::SUCCESS;
    }

    /**
     * @required
     * @param NcziVaccinationsClient $ncziVaccinationsClient
     */
    public function setNcziVaccinationsClient(NcziVaccinationsClient $ncziVaccinationsClient)
    {
        $this->ncziVaccinationsClient = $ncziVaccinationsClient;
    }

    /**
     * @required
     * @param IzaVaccinationsClient $izaVaccinationsClient
     */
    public function setIzaVaccinationsClient(IzaVaccinationsClient $izaVaccinationsClient)
    {
        $this->izaVaccinationsClient = $izaVaccinationsClient;
    }

    /**
     * @required
     * @param PowerBiVaccinationsClient $powerBiVaccinationsClient
     */
    public function setPowerBiVaccinationsClient(PowerBiVaccinationsClient $powerBiVaccinationsClient)
    {
        $this->powerBiVaccinationsClient = $powerBiVaccinationsClient;
    }

    /**
     * @required
     * @param SlovakiaNcziVaccinationsRepository $slovakiaNcziVaccinationsRepository
     */
    public function setSlovakiaNcziVaccinationsRepository(SlovakiaNcziVaccinationsRepository $slovakiaNcziVaccinationsRepository)
    {
        $this->slovakiaNcziVaccinationsRepository = $slovakiaNcziVaccinationsRepository;
    }
}