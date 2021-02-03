<?php

namespace App\Client\Iza;

use Generator;
use League\Csv\Reader;
use League\Csv\Statement;

abstract class AbstractClient extends \App\Client\AbstractClient
{
    const CSV_FILE = null;

    public function findAll(): Generator
    {
        $csv = Reader::createFromString($this->csvContent());
        $csv->setDelimiter(';');

        $items = Statement::create()->process($csv, array_map('strtoupper', $csv->fetchOne()));

        foreach ($items as $i => $item) {
            if ($i > 0) {
                yield $this->dataToEntities($item);
            }
        }
    }

    private function csvContent(): string
    {
        return $this->cached('csvContent---' . md5(static::CSV_FILE), function() {
            return $this->content->load(static::CSV_FILE);
        }, self::FIVE_MINUTES);
    }

    abstract protected function dataToEntities(array $item): array;
}