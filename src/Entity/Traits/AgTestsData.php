<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

trait AgTestsData
{
    /**
     * Počet pozitívnych výsledkov AG testov pre daný deň a okres
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $positivesCount;

    /**
     * Počet negatívnych výsledkov AG testov pre daný deň a okres
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $negativesCount;

    /**
     * Súčet pozitívnych výsledkov AG testov od začiatku testovania do daného dňa
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $positivesSum;

    /**
     * Súčet negatívnych výsledkov AG testov od začiatku testovania do daného dňa
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $negativesSum;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @Serializer\Exclude()
     *
     * @var int
     */
    private $positivityRate;

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

    /**
     * Percentuálny podiel počtu pozitívnych výsledkov z počtu všetkých výsledkov (positivesCount / (positivesCount + negativesCount) * 100
     *
     * @Serializer\VirtualProperty(name="asdasd")
     * @Serializer\Type("float")
     */
    public function positivityRate(): ?float
    {
        return null === $this->positivityRate ? null : round($this->positivityRate / 1000, 3);
    }

    public function getPositivityRate(): ?int
    {
        return $this->positivityRate;
    }

    public function setPositivityRate(?int $positivityRate): self
    {
        $this->positivityRate = $positivityRate;
        return $this;
    }

    public function getPositivesSum(): int
    {
        return $this->positivesSum;
    }

    public function setPositivesSum(int $positivesSum): self
    {
        $this->positivesSum = $positivesSum;
        return $this;
    }

    public function getNegativesSum(): int
    {
        return $this->negativesSum;
    }

    public function setNegativesSum(int $negativesSum): self
    {
        $this->negativesSum = $negativesSum;
        return $this;
    }
}
