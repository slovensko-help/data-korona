<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity()
 */
class Vaccine
{
    /**
     * Interné id záznamu
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @var int|null
     */
    private $id;

    /**
     * @ORM\Column(type="string", unique=true, options={"charset"="ascii"})
     * @Serializer\Exclude()
     *
     * @var string
     */
    private $code;
    /**
     * Názov vakcíny
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $title;
    /**
     * Výrobca vakcíny
     *
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $manufacturer;

    public function getCode(): string
    {
        return $this->code;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getManufacturer(): string
    {
        return $this->manufacturer;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function setManufacturer(string $manufacturer): self
    {
        $this->manufacturer = $manufacturer;
        return $this;
    }
}
