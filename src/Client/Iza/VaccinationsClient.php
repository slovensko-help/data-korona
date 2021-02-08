<?php

namespace App\Client\Iza;

use App\Entity\Raw\IzaVaccinations;
use App\Entity\Region;
use App\Tool\DateTime;
use App\Tool\Id;

class VaccinationsClient extends AbstractClient
{
    const CSV_FILE = 'Vaccination/OpenData_Slovakia_Vaccination_Regions.csv';

    public function entities(): callable
    {
        return function (array $_) {
            yield 'code' => function (Region $region) use ($_) {
                return $region
                    ->setCode($_['REGION_CODE']);
            };
            yield 'code' => function (IzaVaccinations $vaccinations, Region $region) use ($_) {
                $publishedOn = DateTime::dateTimeFromString($_['DATE'], 'Y-m-d', true);
                return $vaccinations
                    ->setRegion($region)
                    ->setCode(Id::fromDateTimeAndInt($publishedOn, $region->getId()))
                    ->setPublishedOn($publishedOn)
                    ->setDose1Count((int)$_['FIRST_DOSE'])
                    ->setDose2Count((int)$_['SECOND_DOSE']);
            };
        };
    }
}