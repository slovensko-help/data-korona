<?php

namespace App\Command;

use App\Entity\Region;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RegionsImport extends AbstractImport
{
    const CSV_FILE = '@project_dir/data/regions.csv';

    protected static $defaultName = 'app:import:regions';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->log('Reading CSV file...'));
        $csvContent = $this->content->load(self::CSV_FILE);
        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating regions...'));
        $this->updateEntities($csvContent);
        $output->writeln($this->log('DONE.'));

        return self::SUCCESS;
    }

    private function updateEntities(string $csvContent)
    {
        $entityClasses = null;
        $cachedEntities = [];
        $syncedEntities = [];

        foreach ($this->csvRecords($csvContent) as $_) {
            foreach ($this->entities($_) as $index => $entity) {
                $this->persistRecordEntities($this->entities($_), $entityClasses, $cachedEntities, $index, $syncedEntities);
            }
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    private function entities($_)
    {
        return [
            [Region::class, 'code', function (Region $region) use ($_) {
                return $region
                    ->setAbbreviation($_['REGION_ABBREVIATION'])
                    ->setCode($_['REGION_CODE'])
                    ->setTitle($_['REGION_NAME']);
            }],
        ];
    }
}