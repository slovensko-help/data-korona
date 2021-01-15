<?php

namespace App\Entity\Aggregation;

use App\Entity\District;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

abstract class AbstractDistrictData extends AbstractData
{

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\District")
     *
     * @Serializer\Exclude()
     *
     * @var District
     */
    protected $district;

    public function getDistrict(): District
    {
        return $this->district;
    }

    public function setDistrict(District $district): self
    {
        $this->district = $district;
        return $this;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Type("int")
     */
    public function getDistrictId(): int
    {
        return $this->district->getId();
    }
}