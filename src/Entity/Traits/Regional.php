<?php

namespace App\Entity\Traits;

use App\Entity\Region;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

trait Regional
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Region")
     *
     * @Serializer\Exclude()
     *
     * @var Region
     */
    protected $region;

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function setRegion(Region $region): self
    {
        $this->region = $region;
        return $this;
    }

    /**
     * Interné id regiónu z regiónov z /api/regions
     *
     * @Serializer\VirtualProperty()
     * @Serializer\Type("int")
     */
    public function getRegionId(): int
    {
        return $this->region->getId();
    }
}