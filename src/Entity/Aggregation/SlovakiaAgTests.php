<?php

namespace App\Entity\Aggregation;

use App\Entity\Traits\AgTestsData;
use App\Entity\Traits\Datetimeable;
use App\Entity\Traits\Publishable;
use App\Entity\Traits\Timestampable;
use DateTimeImmutable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TimeSeries\AgTestsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class SlovakiaAgTests
{
    use Timestampable;
    use Datetimeable;
    use Publishable;
    use AgTestsData;

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
