<?php

namespace App\Client\Iza;

use App\Tool\DateTime;

class VaccinationsClient extends AbstractClient
{
    const CSV_FILE = 'https://raw.githubusercontent.com/Institut-Zdravotnych-Analyz/covid19-data/main/OpenData_Slovakia_Vaccination_Regions.csv';

    protected function dataItemToEntities(array $dataItem): array
    {
        return [
            'published_on' => DateTime::dateTimeFromString($dataItem['DATE'], 'Y-m-d', true),
            'region_code' => $dataItem['REGION_CODE'],
            'dose_1_count' => (int)$dataItem['FIRST_DOSE'],
            'dose_2_count' => (int)$dataItem['SECOND_DOSE'],
        ];
    }
}