<?php

namespace App\Command;

use App\Entity\Region;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportInternalRegionsCsv extends AbstractImportTimeSeries
{
    const CSV_FILE = '@project_dir/data/regions.csv';

    protected static $defaultName = 'app:import:internal:regions';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->log('Reading CSV file...'));

        $csv = $this->fileContent(self::CSV_FILE);

        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating regions...'));

        /**
         * all record columns are referenced ONLY by uppercase keys
         */
        foreach ($this->csvRecords($csv) as $record) {
            $this->region($record);
        }

        $this->entityManager->flush();
        $output->writeln($this->log('DONE.'));

        return self::SUCCESS;
    }

    protected function region(array $record): ?Region
    {
        return $this->updateOrCreate(function (?Region $region) use ($record) {
            return ($region ?? new Region())
                ->setAbbreviation($record['REGION_ABBREVIATION'])
                ->setCode($record['REGION_CODE'])
                ->setTitle($record['REGION_NAME']);
        }, $this->regionRepository, ['code' => $record['REGION_CODE']], true);
    }
}