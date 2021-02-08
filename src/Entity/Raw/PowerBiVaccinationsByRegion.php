<?php

namespace App\Entity\Raw;

use App\Entity\Region;
use App\Entity\Traits\Datetimeable;
use App\Entity\Traits\Timestampable;
use App\Entity\Vaccine;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class PowerBiVaccinationsByRegion
{
    use Timestampable;
    use Datetimeable;

    /**
     * @ORM\Id
     * @ORM\Column(type="string", options={"charset"="ascii"})
     * @var string|null
     */
    private $code = null;

    /**
     * @ORM\Column(type="date_immutable")
     *
     * @var DateTimeImmutable
     */

    private $publishedOn;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $dose1Count;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $dose2Count;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Region")
     * @ORM\JoinColumn(nullable=false)
     *
     * @Serializer\Exclude()
     *
     * @var Region
     */
    private $region;

    public function getRegion(): Region
    {
        return $this->region;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Type("int")
     */
    public function getRegionId(): int
    {
        return $this->region->getId();
    }

    public function setRegion(Region $region): self
    {
        $this->region = $region;
        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getPublishedOn(): DateTimeImmutable
    {
        return $this->publishedOn;
    }

    public function getDose1Count(): int
    {
        return $this->dose1Count;
    }

    public function getDose2Count(): int
    {
        return $this->dose2Count;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function setPublishedOn(DateTimeImmutable $publishedOn): self
    {
        return $this->updateDateTime($this->publishedOn, $publishedOn);
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