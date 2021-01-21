<?php

namespace App\Entity\Traits;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations\Property;

trait Publishable
{
    /**
     * Deň, pre ktorý sú dáta záznamu publikované pre potreby štatistík
     *
     * @ORM\Column(type="date_immutable")
     * @Serializer\Type("DateTimeImmutable<'Y-m-d'>")
     * @Property(example="2020-01-13")
     *
     * @var DateTimeImmutable
     */
    protected $publishedOn;

    public function getPublishedOn(): DateTimeImmutable
    {
        return $this->publishedOn;
    }

    public function setPublishedOn(DateTimeImmutable $publishedOn): self
    {
        if (null === $this->publishedOn || $publishedOn < $this->publishedOn || $publishedOn > $this->publishedOn) {
            $this->publishedOn = $publishedOn;
        }

        return $this;
    }
}