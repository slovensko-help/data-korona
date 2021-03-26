<?php

namespace App\Client\Iza;

use App\Entity\District;
use App\Entity\Raw\IzaAgTests;
use App\Tool\DateTime;
use DateTimeImmutable;

class AgTestsClient extends AbstractClient
{
    const CSV_FILE = 'AG_Tests/OpenData_Slovakia_Covid_AgTests_District.csv';

    public function entities(): callable
    {
        return function (array $_) {
            yield 'code:readonly' => function (District $district) use ($_) {
                return $district
                    ->setCode($_['DISTRICT_CODE']);
            };
            yield 'code' => function (IzaAgTests $agTests, ?District $district) use ($_) {
                $publishedOn = DateTime::dateTimeFromString($_['DATE'], 'Y-m-d', true);
                return $agTests
                    ->setDistrict($district)
                    ->setCode($this->code($publishedOn, $district))
                    ->setPublishedOn($publishedOn)
                    ->setNegativesCount((int)$_['AG_NEG'])
                    ->setPositivesCount((int)$_['AG_POS']);
            };
        };
    }

    private function code(DateTimeImmutable $publishedOn, ?District $district)
    {
        return sprintf('%s-%s',
            $publishedOn->format('Ymd'),
            str_pad(null === $district ? '0' : (string)$district->getId(), 4, '0', STR_PAD_LEFT)
        );
    }
}
