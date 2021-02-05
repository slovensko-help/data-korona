<?php

namespace App\Tool;

use DateTimeImmutable;
use Exception;

class Id
{
    public static function fromDateTimeAndInts(DateTimeImmutable $date, array $integers, array $maxDigits): int
    {
        if (count($integers) !== count($maxDigits)) {
            throw new Exception('Every integer requires maximum.');
        }

        $integers[] = (int)$date->format('ymd');
        $maxDigits[] = 6; // never really used - just for date integer

        $base = 1;
        $result = 0;
        foreach ($integers as $index => $integer) {
            $maximum = pow(10, $maxDigits[$index]);

            if ($integer > $maximum) {
                throw new Exception('Integer is larger than maximum.');
            }

            $result += $integer * $base;
            $base *= $maximum;
        }

        return $result;
    }

    public static function fromDateTimeAndInt(DateTimeImmutable $date, int $integer): int
    {
        return self::fromDateTimeAndInts($date, [$integer], [4]);
    }
}