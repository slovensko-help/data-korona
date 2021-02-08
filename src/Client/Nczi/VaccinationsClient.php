<?php

namespace App\Client\Nczi;

use App\Entity\Raw\NcziVaccinations;
use App\Tool\DateTime;

class VaccinationsClient extends AbstractKpiDataClient
{
    const KPI_ID = 31;

    public function entities(): callable
    {
        return function($_) {
            yield 'id' => function(NcziVaccinations $vaccinations) use ($_) {
                $publishedOn = DateTime::dateTimeFromString($_['date'], 'Y-m-d', true);
                return $vaccinations
                    ->setId((int)$publishedOn->format('Ymd'))
                    ->setPublishedOn($publishedOn)
                    ->setDose1Count($this->nullOrInt($_['v']) + $this->nullOrInt($_['v1']))
                    ->setDose2Count($this->nullOrInt($_['v1']));
            };
        };
    }
}