<?php

namespace App\Client\Nczi;

use App\Tool\DateTime;

class VaccinationsClient extends AbstractKpiDataClient
{
    const KPI_ID = 31;

    protected function hydrateItem(array $data): array
    {
        return [
            'published_on' => DateTime::dateTimeFromString($data['date'], 'Y-m-d', true),
            'dose_1_count' => $data['v'],
            'dose_2_count' => $data['v1'],
        ];
    }
}