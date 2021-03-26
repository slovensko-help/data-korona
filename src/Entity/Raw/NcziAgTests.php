<?php

namespace App\Entity\Raw;

use App\Entity\District;
use App\Entity\Traits\Datetimeable;
use App\Entity\Traits\Timestampable;
use DateTimeImmutable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @Auditable()
 */
class NcziAgTests
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
    private $positivesSum;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $negativesSum;

    public static function calculateId(DateTimeImmutable $publishedOn): int
    {
        return (int)$publishedOn->format('Ymd');
    }

    public function getId(): ?int
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

    public function getPublishedOn(): DateTimeImmutable
    {
        return $this->publishedOn;
    }

    public function getPositivesSum(): int
    {
        return $this->positivesSum;
    }

    public function getNegativesSum(): int
    {
        return $this->negativesSum;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setPublishedOn(DateTimeImmutable $publishedOn): self
    {
        return $this->updateDateTime($this->publishedOn, $publishedOn);
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

    public function setPositivesSum(int $positivesSum): self
    {
        $this->positivesSum = $positivesSum;
        return $this;
    }

    public function setNegativesSum(int $negativesSum): self
    {
        $this->negativesSum = $negativesSum;
        return $this;
    }
}
