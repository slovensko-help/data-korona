<?php

namespace App\Entity\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations\Property;

trait Timestampable {
    /**
     * @ORM\Column(type="datetime_immutable")
     * @Serializer\Type("DateTimeImmutable<'Y-m-d H:i:s'>")
     * @Property(example="2020-01-13 12:34:56")
     * @Serializer\Exclude()
     *
     * @var DateTimeImmutable
     */
    protected $createdAt;

    /**
     * Čas poslednej aktualizácie záznamu
     *
     * @ORM\Column(type="datetime_immutable")
     * @Serializer\Type("DateTimeImmutable<'Y-m-d H:i:s'>")
     * @Property(example="2020-01-13 12:34:56")
     *
     * @var DateTimeImmutable
     */
    protected $updatedAt;

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updatedTimestamps(): void
    {
        $now = new DateTimeImmutable('now');

        $this->updatedAt = $now;
        if ($this->createdAt === null) {
            $this->createdAt = $now;
        }
    }
}