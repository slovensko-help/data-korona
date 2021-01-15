<?php

namespace App\Entity\Aggregation;

use App\Entity\Region;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

abstract class AbstractRegionData extends AbstractData
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
     * @Serializer\VirtualProperty()
     * @Serializer\Type("int")
     */
    public function getRegionId(): int
    {
        return $this->region->getId();
    }
}