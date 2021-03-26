<?php

namespace App\Command;

use App\Client\PowerBi\DebugClient;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PowerBiDebug extends AbstractImport
{
    protected static $defaultName = 'app:debug:powerbi';

    /** @var DebugClient */
    protected $powerBiClient;

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

        return self::SUCCESS;
    }

    /**
     * @required
     * @param DebugClient $powerBiClient
     */
    public function setPowerBiClient(DebugClient $powerBiClient)
    {
        $this->powerBiClient = $powerBiClient;
    }
}
