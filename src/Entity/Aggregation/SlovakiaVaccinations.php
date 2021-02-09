<?php

declare(strict_types=1);

namespace App\Entity\Aggregation;

use App\Entity\Traits\Publishable;
use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class SlovakiaVaccinations
{
    use Timestampable;
    use Publishable;

    /**
     * Interné id záznamu
     *
     * @ORM\Id()
     * @ORM\Column(type="string", options={"charset"="ascii"})
     *
     * @var string
     */
    protected $id;

    /**
     * Počet podaných prvých dávok vakcín pre daný deň
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $dose1Count;

    /**
     * Počet podaných druhých dávok vakcín pre daný deň
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $dose2Count;

    /**
     * Súčet podaných prvých dávok vakcín od začiatku vakcinácie do daného dňa
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $dose1Sum;

    /**
     * Súčet podaných druhých dávok vakcín od začiatku vakcinácie do daného dňa
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $dose2Sum;

    public function getId(): string
    {
        return $this->id;
    }

    public function getDose1Count(): ?int
    {
        return $this->dose1Count;
    }

    public function getDose2Count(): ?int
    {
        return $this->dose2Count;
    }

    public function getDose1Sum(): int
    {
        return $this->dose1Sum;
    }

    public function getDose2Sum(): int
    {
        return $this->dose2Sum;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setDose1Count(?int $dose1Count): self
    {
        $this->dose1Count = $dose1Count;
        return $this;
    }

    public function setDose2Count(?int $dose2Count): self
    {
        $this->dose2Count = $dose2Count;
        return $this;
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
