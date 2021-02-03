<?php

namespace App\Client\Iza;

use App\Tool\DateTime;

class VaccinationsClient extends AbstractClient
{
    const CSV_FILE = 'https://raw.githubusercontent.com/Institut-Zdravotnych-Analyz/covid19-data/main/OpenData_Slovakia_Vaccination_Regions.csv';

    protected function dataToEntities(array $data): array
    {
        return [
            'published_on' => DateTime::dateTimeFromString($data['DATE'], 'Y-m-d', true),
            'region_code' => $data['REGION_CODE'],
            'dose_1_count' => (int)$data['FIRST_DOSE'],
            'dose_2_count' => (int)$data['SECOND_DOSE'],
        ];
    }
}