<?php

namespace App\Entity\Raw;

use App\Entity\Traits\Datetimeable;
use App\Entity\Traits\Timestampable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class NcziVaccinations
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
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $dose1Sum;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $dose2Sum;

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

    public function getDose1Sum(): int
    {
        return $this->dose1Sum;
    }

    public function getDose2Sum(): int
    {
        return $this->dose2Sum;
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

    public function setDose1Sum(int $dose1Sum): self
    {
        $this->dose1Sum = $dose1Sum;
        return $this;
    }

    public function setDose2Sum(int $dose2Sum): self
    {
        $this->dose2Sum = $dose2Sum;
        return $this;
    }
}