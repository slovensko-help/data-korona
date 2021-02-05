<?php

namespace App\Client\Nczi;

use App\Entity\Raw\NcziVaccinations;
use App\Tool\DateTime;

class VaccinationsClient extends AbstractKpiDataClient
{
    const KPI_ID = 31;

    protected function dataItemToEntities(array $dataItem): array
    {
        return [
            [NcziVaccinations::class, function(NcziVaccinations $entity) use ($dataItem) {
                $publishedOn = DateTime::dateTimeFromString($dataItem['date'], 'Y-m-d', true);
                return $entity
                    ->setId((int)$publishedOn->format('Ymd'))
                    ->setPublishedOn($publishedOn)
                    ->setDose1Count($this->nullOrInt($dataItem['v']))
                    ->setDose2Count($this->nullOrInt($dataItem['v1']));
            }],
        ];
    }
}