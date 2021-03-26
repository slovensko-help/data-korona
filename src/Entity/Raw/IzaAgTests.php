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
class IzaAgTests
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
    private $positivesCount;

    /**
     * @ORM\Column(type="integer")
     *
     * @var integer
     */
    private $negativesCount;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\District")
     * @ORM\JoinColumn(nullable=true)
     *
     * @Serializer\Exclude()
     *
     * @var District
     */
    private $district;

    public function getDistrict(): ?District
    {
        return $this->district;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\Type("int")
     */
    public function getDistrictId(): ?int
    {
        return null === $this->district ? null : $this->district->getId();
    }

    public function setDistrict(?District $district): self
    {
        $this->district = $district;
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

    public function setCode(string $code): self
    {
        $this->code = $code;
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

    public function getPositivesCount(): int
    {
        return $this->positivesCount;
    }

    public function setPositivesCount(int $positivesCount): self
    {
        $this->positivesCount = $positivesCount;
        return $this;
    }

    public function getNegativesCount(): int
    {
        return $this->negativesCount;
    }

    public function setNegativesCount(int $negativesCount): self
    {
        $this->negativesCount = $negativesCount;
        return $this;
    }
}
