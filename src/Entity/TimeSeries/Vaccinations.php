<?php

namespace App\Entity\TimeSeries;

use App\Entity\Region;
use App\Entity\Traits\Datetimeable;
use App\Entity\Traits\Publishable;
use App\Entity\Traits\Regional;
use App\Entity\Traits\Timestampable;
use App\Entity\Vaccine;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimeSeries\VaccinationsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Vaccinations
{
    use Timestampable;
    use Datetimeable;
    use Publishable;
    use Regional;

    /**
     * Interné id záznamu
     *
     * @ORM\Id
     * @ORM\Column(type="string", options={"charset"="ascii"})
     * @var string|null
     */
    private $id = null;

    /**
     * Počet podaných prvých dávok vakcín pre daný deň, kraj a typ vakcíny
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $dose1Count;

    /**
     * Počet podaných druhých dávok vakcín pre daný deň, kraj a typ vakcíny
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $dose2Count;

    /**
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Vaccine")
     * @ORM\JoinColumn(nullable=false)
     * @Serializer\Exclude()
     *
     * @var Vaccine
     */
    private $vaccine;

    /**
     * Interné id vakcíny z /api/vaccines
     *
     * @Serializer\VirtualProperty()
     * @Serializer\Type("int")
     */
    public function getVaccineId(): int
    {
        return $this->vaccine->getId();
    }

    public function getVaccine(): Vaccine
    {
        return $this->vaccine;
    }

    public function getRegion(): Region
    {
        return $this->region;
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDose1Count(): int
    {
        return $this->dose1Count;
    }

    public function getDose2Count(): int
    {
        return $this->dose2Count;
    }

    public function setVaccine(Vaccine $vaccine): self
    {
        $this->vaccine = $vaccine;
        return $this;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setDose1Count(int $dose1Count): self
    {
        $this->dose1Count = $dose1Count;
        return $this;
    }

    public function setDose2Count(int $dose2Count): self
    {
        $this->dose2Count = $dose2Count;
        return $this;
    }

    /**
     * @param DateTimeImmutable $createdAt
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param DateTimeImmutable $updatedAt
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}