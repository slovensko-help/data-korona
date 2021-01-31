<?php

namespace App\Tool;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

class DateTime
{
    public static function dateTimeFromString($datetimeString, $format, $resetTime = false): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat($format, $datetimeString, new DateTimeZone('Europe/Bratislava'));

        if (false === $date) {
            throw new Exception('Datetime string "' . $datetimeString . '" is not in format "' . $format . '"');
        }

        if ($resetTime) {
            $date = $date->setTime(0, 0, 0);
        }

        return $date;
    }
}