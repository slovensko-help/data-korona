<?php

namespace App\Client\Nczi;

use App\Entity\Raw\NcziAgTests;
use App\Entity\Raw\NcziVaccinations;
use App\Tool\DateTime;

class AgTestsClient extends AbstractKpiDataClient
{
    const KPI_ID = 32;

    public function entities(): callable
    {
        return function(array $_) {
            yield 'id' => function(NcziAgTests $agTests) use ($_) {
                $publishedOn = DateTime::dateTimeFromString($_['date'], 'Y-m-d', true);
                return $agTests
                    ->setId(NcziAgTests::calculateId($publishedOn))
                    ->setPublishedOn($publishedOn)
                    ->setNegativesSum($this->nullOrInt($_['v']))
                    ->setPositivesSum($this->nullOrInt($_['v1']));
            };
        };
    }
}
