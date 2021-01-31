<?php

namespace App\Entity\Raw;

use App\Entity\Traits\Datetimeable;
use App\Entity\Traits\Timestampable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Raw\SlovakiaNcziVaccinationsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class SlovakiaNcziVaccinations
{
    use Timestampable;
    use Datetimeable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @var int|null
     */
    private $id = null;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
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

    /**
     * @param DateTimeImmutable $createdAt
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @param DateTimeImmutable $updatedAt
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
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
}