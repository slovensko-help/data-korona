<?php

namespace App\Persister;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class EntityPersisterFactory
{
    public function __construct(EntityManagerInterface $entityManager, PropertyAccessorInterface $propertyAccessor)
    {
        $this->entityManager = $entityManager;
        $this->propertyAccessor = $propertyAccessor;
    }

    public function createPersister()
    {
        return new EntityPersister($this->entityManager, $this->propertyAccessor);
    }
}