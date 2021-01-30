<?php

namespace App\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportPowerBiVaccines extends AbstractImportTimeSeries
{
    protected static $defaultName = 'app:import:powerbi:vaccines';

    protected function configure()
    {
        parent::configure();

        $this->addOption('dump-schema');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('dump-schema')) {
            $this->dumpPowerBiSchema($this->powerBiVaccineRepository, $output);
            return self::SUCCESS;
        }

//        $result[] = [
//            'Datum',
//            'Kraj',
////            'Vakcina',
//            '1. davka',
//            '2. davka',
//        ];

        $result = [];
        foreach ($this->powerBiVaccineRepository->findAll() as $item) {
            $item[0] = date('Y-m-d', $item[0] / 1000);
            $result[] = $item;
        }

        $table = (new Table($output));

        if (0 !== count($result)) {
//            $table->setHeaders(array_shift($result));
            $table->setRows($result);
        }

        $table->render();

        return self::SUCCESS;
    }
}