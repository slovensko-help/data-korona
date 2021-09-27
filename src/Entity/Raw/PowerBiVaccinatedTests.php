<?php

namespace App\Entity\Raw;

use App\Entity\Traits\AgTestsData;
use App\Entity\Traits\Datetimeable;
use App\Entity\Traits\Publishable;
use App\Entity\Traits\Timestampable;
use DateTimeImmutable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class PowerBiVaccinatedTests
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
     * @ORM\Column(type="string")
     * @var string
     */
    private $testType;

    /**
     * @ORM\Column(type="string")
     * @var string
     */
    private $testResult;

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

    public function getTestType(): string
    {
        return $this->testType;
    }

    public function setTestType(string $testType): self
    {
        $this->testType = $testType;
        return $this;
    }

    public function getTestResult(): string
    {
        return $this->testResult;
    }

    public function setTestResult(string $testResult): self
    {
        $this->testResult = $testResult;
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

    /**
     * @param DateTimeImmutable $createdAt
     * @return SlovakiaAgTests
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param DateTimeImmutable $updatedAt
     * @return SlovakiaAgTests
     */
    public function setUpdatedAt(DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
