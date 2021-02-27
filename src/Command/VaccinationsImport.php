<?php

namespace App\Command;

use App\Client\Iza\VaccinationsClient as IzaVaccinationsClient;
use App\Client\Nczi\VaccinationsClient as NcziVaccinationsClient;
use App\Client\PowerBi\VaccinationsClient as PowerBiVaccinationsClient;
use App\Entity\Raw\IzaVaccinations;
use App\Entity\Raw\NcziVaccinations;
use App\Entity\Raw\PowerBiVaccinations;
use App\Entity\TimeSeries\Vaccinations;
use DateInterval;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VaccinationsImport extends AbstractImport
{
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
        $this->addOption('debug-powerbi');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->disableDoctrineLogger();

        if ($input->getOption('dump-powerbi-schema')) {
            $this->dumpPowerBiSchema($this->powerBiClient, $output);
            return self::SUCCESS;
        }

        if ($input->getOption('debug-powerbi')) {
            $rows = [];

            foreach ($this->powerBiClient->debug() as $row) {
                $row[0] = date('Y-m-d', $row[0] / 1000);
                $rows[] = $row;
            }

            (new Table($output))
                ->setRows($rows)
                ->render();
            return self::SUCCESS;
        }

        $output->writeln($this->log('Updating powerBi vaccinactions.'));
        $this->persist(
            $this->powerBiClient->findAllByRegionAndVaccine(),
            $this->powerBiClient->entitiesByRegionAndVaccine(),
            [PowerBiVaccinations::class => [null, null],]
        );
        $output->writeln($this->log('DONE.'));

        $ncziFrom = (new DateTimeImmutable('-40 days', new DateTimeZone('Europe/Bratislava')))->setTime(0, 0);
        $ncziTo = (new DateTimeImmutable('tomorrow', new DateTimeZone('Europe/Bratislava')))->setTime(0, 0);

        $output->writeln($this->log('Updating NCZI vaccinactions.'));
        $this->persist(
            $this->ncziClient->findAll($ncziFrom, $ncziTo),
            $this->ncziClient->entities(),
            [NcziVaccinations::class => [NcziVaccinations::calculateId($ncziFrom), NcziVaccinations::calculateId($ncziTo->sub(new DateInterval('P1D')))],]
        );
        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating IZA vaccinactions.'));
        $this->persist(
            $this->izaClient->findAll(),
            $this->izaClient->entities(),
            [IzaVaccinations::class => [null, null],]);
        $output->writeln($this->log('DONE.'));

        $vaccinationsRepository = $this->entityManager->getRepository(Vaccinations::class);

        $output->writeln($this->log('Updating slovakiaVaccinactions.'));
        // slovakiaVaccinations must be imported first because regionVaccinations and vaccinations depend on it
        $this->persist(
            $vaccinationsRepository->slovakiaVaccinations(),
            $vaccinationsRepository->slovakiaVaccinationsEntities()
        );
        $output->writeln($this->log('DONE.'));
        $output->writeln($this->log('Updating regionVaccinations.'));
        $this->persist(
            $vaccinationsRepository->regionVaccinations(),
            $vaccinationsRepository->regionVaccinationsEntities()
        );
        $output->writeln($this->log('DONE.'));
        $output->writeln($this->log('Updating vaccinactions.'));
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
