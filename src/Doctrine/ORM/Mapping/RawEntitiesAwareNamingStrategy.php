<?php

namespace App\Doctrine\ORM\Mapping;

use Doctrine\ORM\Mapping\UnderscoreNamingStrategy;

class RawEntitiesAwareNamingStrategy extends UnderscoreNamingStrategy
{
    const RAW_ENTITY_PREFIX = 'App\Entity\Raw\\';

    public function __construct()
    {
        parent::__construct(CASE_LOWER, true);
    }

    public function classToTableName($className)
    {
        if (0 === strpos($className, self::RAW_ENTITY_PREFIX)) {
            $className = 'Raw' . substr($className, strlen(self::RAW_ENTITY_PREFIX));
        }

        return parent::classToTableName($className);
    }
}