<?php

namespace App\Entity\Traits;

use App\Entity\District;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

trait Districtual
{
    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\District")
     *
     * @Serializer\Exclude()
     *
     * @var District
     */
    protected $district;

    public function getDistrict(): ?District
    {
        return $this->district;
    }

    public function setDistrict(?District $district): self
    {
        $this->district = $district;
        return $this;
    }

    /**
     * Interné id okresu z /api/districts alebo null. Hodnota null znamená, že dáta nie sú priradené žiadnemu okresu.
     *
     * @Serializer\VirtualProperty()
     * @Serializer\Type("int")
     */
    public function getDistrictId(): ?int
    {
        return null === $this->district ? null : $this->district->getId();
    }
}
