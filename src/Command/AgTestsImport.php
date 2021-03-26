<?php

namespace App\Command;

use App\Client\Iza\AgTestsClient as IzaAgTestsClient;
use App\Client\Nczi\AgTestsClient as NcziAgTestsClient;
use App\Client\PowerBi\AgTestsClient as PowerBiAgTestsClient;
use App\Entity\Aggregation\SlovakiaAgTests;
use App\Entity\Raw\IzaAgTests;
use App\Entity\Raw\PowerBiAgTests;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AgTestsImport extends AbstractImport
{
    protected static $defaultName = 'app:import:ag-tests';

    /** @var PowerBiAgTestsClient */
    protected $powerBiClient;

    /** @var NcziAgTestsClient */
    protected $ncziClient;

    /** @var IzaAgTestsClient */
    protected $izaClient;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->disableDoctrineLogger();

        $output->writeln($this->log('Updating powerBi AG tests.'));
        $this->persist(
            $this->powerBiClient->findAll(),
            $this->powerBiClient->entities(),
            [PowerBiAgTests::class => [null, null, true],]
        );
        $output->writeln($this->log('DONE.'));

        $ncziFrom = (new DateTimeImmutable('-40 days', new DateTimeZone('Europe/Bratislava')))->setTime(0, 0);
        $ncziTo = (new DateTimeImmutable('tomorrow', new DateTimeZone('Europe/Bratislava')))->setTime(0, 0);

        $output->writeln($this->log('Updating NCZI AG tests.'));
        $this->persist(
            $this->ncziClient->findAll($ncziFrom, $ncziTo),
            $this->ncziClient->entities()
        );
        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating IZA AG tests.'));
        $this->persist(
            $this->izaClient->findAll(),
            $this->izaClient->entities(),
            [IzaAgTests::class => [null, null, true],]);
        $output->writeln($this->log('DONE.'));

        $agTestsRepository = $this->entityManager->getRepository(SlovakiaAgTests::class);

        $output->writeln($this->log('Updating AG tests.'));
        // slovakiaAgTests must be imported first because districtAgTests and agTests depend on it
        $this->persist(
            $agTestsRepository->slovakiaAgTests(),
            $agTestsRepository->slovakiaAgTestsEntities()
        );
        $output->writeln($this->log('DONE.'));
        $output->writeln($this->log('Updating regionAgTests.'));
        $this->persist(
            $agTestsRepository->regionAgTests(),
            $agTestsRepository->regionAgTestsEntities()
        );
        $output->writeln($this->log('DONE.'));
        $output->writeln($this->log('Updating districtAgTests.'));
        $this->persist(
            $agTestsRepository->districtAgTests(),
            $agTestsRepository->districtAgTestsEntities()
        );
        $output->writeln($this->log('DONE.'));

        return self::SUCCESS;
    }

    /**
     * @required
     * @param NcziAgTestsClient $ncziClient
     */
    public function setNcziClient(NcziAgTestsClient $ncziClient)
    {
        $this->ncziClient = $ncziClient;
    }

    /**
     * @required
     * @param IzaAgTestsClient $izaClient
     */
    public function setIzaClient(IzaAgTestsClient $izaClient)
    {
        $this->izaClient = $izaClient;
    }

    /**
     * @required
     * @param PowerBiAgTestsClient $powerBiClient
     */
    public function setPowerBiClient(PowerBiAgTestsClient $powerBiClient)
    {
        $this->powerBiClient = $powerBiClient;
    }
}
