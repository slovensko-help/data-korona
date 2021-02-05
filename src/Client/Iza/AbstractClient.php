<?php

namespace App\Client\Iza;

use Generator;
use League\Csv\Reader;
use League\Csv\Statement;

abstract class AbstractClient extends \App\Client\AbstractClient
{
    const CSV_BASE_URL = 'https://raw.githubusercontent.com/Institut-Zdravotnych-Analyz/covid19-data/main/';
    const CSV_FILE = null;
    const CSV_DELIMITER = ';';
    const CSV_HEADER_OFFSET = 0;

    public function findAll(): Generator
    {
        $csv = Reader::createFromString($this->csvContent());
        $csv->setDelimiter(static::CSV_DELIMITER);
        $csv->setHeaderOffset(static::CSV_HEADER_OFFSET);

        yield from Statement::create()->process($csv, array_map('strtoupper', $csv->getHeader()));
    }

    private function csvContent(): string
    {
        return $this->cached('csvContent---' . md5($this->csvUrl()), function () {
            return $this->content->load($this->csvUrl());
        }, self::FIVE_MINUTES);
    }

    private function csvUrl()
    {
        return static::CSV_BASE_URL . static::CSV_FILE;
    }
}