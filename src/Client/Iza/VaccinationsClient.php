<?php

namespace App\Client\Iza;

use App\Entity\Raw\IzaVaccinations;
use App\Entity\Region;
use App\Tool\DateTime;
use App\Tool\Id;
use Generator;
use League\Csv\Reader;
use League\Csv\Statement;

class VaccinationsClient extends AbstractClient
{
    const CSV_FILE = 'Vaccination/OpenData_Slovakia_Vaccination_Regions.csv';

    // QUICK FIX for vaccines
    public function findAll(): Generator
    {
        $result = [];

        foreach (parent::findAll() as $item) {
            $key = $item['REGION_CODE'] . '-' . $item['DATE'];

            if (!isset($result[$key])) {
                $result[$key] = [
                    'REGION_CODE' => $item['REGION_CODE'],
                    'DATE' => $item['DATE'],
                    'FIRST_DOSE' => 0,
                    'SECOND_DOSE' => 0,
                ];
            }

            $result[$key]['FIRST_DOSE'] += $item['FIRST_DOSE'];
            $result[$key]['SECOND_DOSE'] += $item['SECOND_DOSE'];
        }

        foreach ($result as $item) {
            yield $item;
        }
    }

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