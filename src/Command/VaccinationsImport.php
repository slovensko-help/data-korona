<?php

namespace App\Command;

use App\Client\Iza\VaccinationsClient as IzaVaccinationsClient;
use App\Client\Nczi\VaccinationsClient as NcziVaccinationsClient;
use App\Client\PowerBi\VaccinationsClient as PowerBiVaccinationsClient;
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

//        $table = new Table($output);
//
//        foreach ($this->powerBiClient->dump() as $row) {
//            $row[0] = date('Y-m-d', $row[0] / 1000);
//            $table->addRow($row);
//        }
//
//        $table->render();
//
//        die;
//
//        $this->ncziClient->findAll();

//        if ($input->getOption('nczi-only')) {
//            $this->slovakiaNcziVaccinationsRepository->saveAll($this->ncziVaccinationsClient->findAll());
//        }

//        $this->commitChangesToDb();

        $cachedEntities = [];
        $entityClasses = null;

//        $cachedEntities[Region::class] = $this->regionRepository->findAllIndexedByCode();

        foreach ($this->batches($this->powerBiClient->findAllByRegion(), 100) as $batchIndex => $batch) {
            foreach ($batch as $i => $entities) {
                $this->persistRecordEntities($entities, $entityClasses, $cachedEntities);
            }

            $output->writeln("Batch: $batchIndex");
            $this->entityManager->flush();
//            $this->commitChangesToDb();
//            $cachedEntities[Region::class] = $this->regionRepository->findAllIndexedByCode();
        }


//        dump(iterator_to_array($this->powerBiVaccinationsClient->findAllByRegion()));die;
//        dump(iterator_to_array($this->ncziVaccinationsClient->findAll()));
//        dump(iterator_to_array($this->izaVaccinationsClient->findAll()));

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