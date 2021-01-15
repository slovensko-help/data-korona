<?php

namespace App\Entity\Traits;

use DateTimeImmutable;

trait Datetimeable {
    protected function updateDateTime(?DateTimeImmutable &$oldValue, DateTimeImmutable $newValue): self {
        if (null === $oldValue || $newValue < $oldValue || $newValue > $oldValue) {
            $oldValue = $newValue;
        }

        return $this;
    }
}