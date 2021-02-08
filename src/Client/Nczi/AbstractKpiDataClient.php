<?php

namespace App\Client\Nczi;

use App\Tool\ArrayChain;
use DateTimeImmutable;
use DateTimeZone;

abstract class AbstractKpiDataClient extends AbstractClient
{
    const KPI_ID = null;

    public function findAll()
    {
        $response = $this->apiContent('https://covid-19.nczisk.sk/webapi/v1/kpi/' . static::KPI_ID . '/data', [
            'from' => (new DateTimeImmutable('-60 days', new DateTimeZone('Europe/Bratislava')))->setTime(0, 0, 0)->format('Y-m-d'),
            'to' => (new DateTimeImmutable('tomorrow', new DateTimeZone('Europe/Bratislava')))->setTime(0, 0, 0)->format('Y-m-d'),
            'period' => 'd',
        ]);

        yield from ArrayChain::value($response, 'd');
    }
}