<?php

namespace App\Entity\Raw;

use App\Entity\Traits\Datetimeable;
use App\Entity\Traits\Publishable;
use App\Entity\Traits\Timestampable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class PowerBiVaccinatedPatients
{
    use Timestampable;
    use Datetimeable;
    use Publishable;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $vaccinationStatus;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @var int
     */
    private $count;

    public function getVaccinationStatus(): string
    {
        return $this->vaccinationStatus;
    }

    public function setVaccinationStatus(string $vaccinationStatus): self
    {
        $this->vaccinationStatus = $vaccinationStatus;
        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;
        return $this;
    }

    /**
     * Interné id záznamu
     *
     * @ORM\Id
     * @ORM\Column(type="string", options={"charset"="ascii"})
     * @var string|null
     */
    private $id = null;

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

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
