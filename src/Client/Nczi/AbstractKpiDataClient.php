<?php

namespace App\Client\Nczi;

use App\Tool\ArrayChain;
use DateTimeImmutable;
use DateTimeZone;

abstract class AbstractKpiDataClient extends AbstractClient
{
    const KPI_ID = null;

    public function findAll(DateTimeImmutable $from, DateTimeImmutable $to)
    {
        $response = $this->apiContent('https://covid-19.nczisk.sk/webapi/v1/kpi/' . static::KPI_ID . '/data', [
            'from' => $from->format('Y-m-d'),
            'to' => $to->format('Y-m-d'),
            'period' => 'd',
        ]);

        yield from ArrayChain::value($response, 'd');
    }
}
