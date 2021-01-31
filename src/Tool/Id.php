<?php

namespace App\Tool;

use DateTimeImmutable;

class Id
{
    public static function fromDateTimeAndInt(DateTimeImmutable $date, int $id): int
    {
        return $id + ((int)$date->format('ymd')) * 10000;
    }
}