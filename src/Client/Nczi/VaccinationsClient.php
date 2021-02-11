<?php

namespace App\Client\Nczi;

use App\Entity\Raw\NcziVaccinations;
use App\Tool\DateTime;

class VaccinationsClient extends AbstractKpiDataClient
{
    const KPI_ID = 31;

    public function entities(): callable
    {
        return function(array $_) {
            yield 'id' => function(NcziVaccinations $vaccinations) use ($_) {
                $publishedOn = DateTime::dateTimeFromString($_['date'], 'Y-m-d', true);
                return $vaccinations
                    ->setId(NcziVaccinations::calculateId($publishedOn))
                    ->setPublishedOn($publishedOn)
                    ->setDose1Sum($this->nullOrInt($_['v']) + $this->nullOrInt($_['v1']))
                    ->setDose2Sum($this->nullOrInt($_['v1']));
            };
        };
    }
}