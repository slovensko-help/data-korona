<?php

namespace App\Entity\Aggregation;

use App\Entity\Traits\Datetimeable;
use App\Entity\Traits\Publishable;
use App\Entity\Traits\Timestampable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations\Property;

abstract class AbstractData
{
    use Datetimeable;
    use Publishable;

    /**
     * Najstarší čas, kedy niektorá nemocnica reportovala záznam v agregácii
     *
     * @ORM\Column(type="datetime_immutable")
     * @Serializer\Type("DateTimeImmutable<'Y-m-d H:i:s'>")
     * @Property(example="2020-01-13 12:34:56")
     *
     * @var DateTimeImmutable
     */
    protected $oldestReportedAt;

    /**
     * * Najnovší čas, kedy niektorá nemocnica reportovala záznam v agregácii
     *
     * @ORM\Column(type="datetime_immutable")
     * @Serializer\Type("DateTimeImmutable<'Y-m-d H:i:s'>")
     * @Property(example="2020-01-13 12:34:56")
     *
     * @var DateTimeImmutable
     */
    protected $newestReportedAt;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getOldestReportedAt(): DateTimeImmutable
    {
        return $this->oldestReportedAt;
    }

    public function setOldestReportedAt(DateTimeImmutable $oldestReportedAt): self
    {
        return $this->updateDateTime($this->oldestReportedAt, $oldestReportedAt);
    }

    public function getNewestReportedAt(): DateTimeImmutable
    {
        return $this->newestReportedAt;
    }

    public function setNewestReportedAt(DateTimeImmutable $newestReportedAt): self
    {
        return $this->updateDateTime($this->newestReportedAt, $newestReportedAt);
    }
}