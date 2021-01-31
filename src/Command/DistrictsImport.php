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

        $csv = $this->content->load(self::CSV_FILE);

        $output->writeln($this->log('DONE.'));

        $regions = [];

        foreach ($this->regionRepository->findAll() as $region) {
            $regions[$region->getCode()] = $region;
        }

        $output->writeln($this->log('Updating districts...'));

        /**
         * all record columns are referenced ONLY by uppercase keys
         */
        foreach ($this->csvRecords($csv) as $record) {
            $this->district(
                $record,
                $regions[$record['REGION_CODE']]
            );
        }

        $this->entityManager->flush();
        $output->writeln($this->log('DONE.'));

        return self::SUCCESS;
    }

    protected function district(array $record, ?Region $region): ?District
    {
        if ($region instanceof Region) {
            return $this->findOrCreate(function () use ($record, $region) {
                return (new District())
                    ->setRegion($region)
                    ->setCode($record['DISTRICT_CODE'])
                    ->setTitle($record['DISTRICT_NAME']);
            }, $this->districtRepository, ['code' => $record['DISTRICT_CODE']]);
        }

        return null;
    }
}