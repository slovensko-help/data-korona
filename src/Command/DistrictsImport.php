<?php

namespace App\Command;

use App\Entity\District;
use App\Entity\Region;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DistrictsImport extends AbstractImport
{
    const CSV_FILE = '@project_dir/data/districts.csv';

    protected static $defaultName = 'app:import:districts';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->log('Reading CSV file...'));
        $csvContent = $this->content->load(self::CSV_FILE);
        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating districts...'));
        $this->persistRecords($this->csvRecords($csvContent));
        $output->writeln($this->log('DONE.'));

        return self::SUCCESS;
    }

    private function persistRecords(iterable $records)
    {
        $entityClasses = null;
        $cachedEntities = [];

        foreach ($records as $_) {
            $this->persistRecordEntities([
                [Region::class, 'code', function (Region $region) use ($_) {
                    return $region
                        ->setCode($_['REGION_CODE']);
                }],
                [District::class, 'code', function (District $district, Region $region) use ($_) {
                    return $district
                        ->setRegion($region)
                        ->setCode($_['DISTRICT_CODE'])
                        ->setTitle($_['DISTRICT_NAME']);
                }],
            ], $entityClasses, $cachedEntities);
        }

        $this->entityManager->flush();
    }
}