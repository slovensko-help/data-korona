<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait HospitalBedsData {
    /**
     * Maximálny počet všetkých lôžok (pre COVID aj neCOVID pacientov)
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * source: ZAR_VOLNE
     */
    private $capacityAll;

    /**
     * Počet všetkých voľných lôžok
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * source: ZAR_VOLNE
     */
    private $freeAll;

    /**
     * Maximálny počet reprofilovaných lôžok pre COVID pacientov
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * source: ZAR_MAX
     */
    private $capacityCovid;

    /**
     * Lôžka na jednotke intenzívnej starostlivosti (JIS) aktuálne využité pacientami, ktorí majú COVID
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * source: COVID_JIS
     */
    private $occupiedJisCovid;

    /**
     * Lôžka na oddelení anesteziológie a intenzívnej medicíny (OAIM), aktuálne využité ventilovanými pacientami, ktorí majú COVID
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * source: COVID_OAIM
     */
    private $occupiedOaimCovid;

    /**
     * Lôžka s kyslíkom (O2) aktuálne využité pacientami, ktorí majú COVID
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * source: COVID_O2
     */
    private $occupiedO2Covid;

    /**
     * Obyčajné lôžka aktuálne využité pacientami, ktorí majú COVID
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * source: COVID_NONO2
     */
    private $occupiedOtherCovid;

    public function getCapacityAll(): ?int
    {
        return $this->capacityAll;
    }

    public function setCapacityAll(?int $capacityAll): self
    {
        $this->capacityAll = $capacityAll;
        return $this;
    }

    public function getCapacityCovid(): ?int
    {
        return $this->capacityCovid;
    }

    public function setCapacityCovid(?int $capacityCovid): self
    {
        $this->capacityCovid = $capacityCovid;
        return $this;
    }

    public function getFreeAll(): ?int
    {
        return $this->freeAll;
    }

    public function setFreeAll(?int $freeAll): self
    {
        $this->freeAll = $freeAll;
        return $this;
    }

    public function getOccupiedJisCovid(): ?int
    {
        return $this->occupiedJisCovid;
    }

    public function setOccupiedJisCovid(?int $occupiedJisCovid): self
    {
        $this->occupiedJisCovid = $occupiedJisCovid;
        return $this;
    }

    public function getOccupiedOaimCovid(): ?int
    {
        return $this->occupiedOaimCovid;
    }

    public function setOccupiedOaimCovid(?int $occupiedOaimCovid): self
    {
        $this->occupiedOaimCovid = $occupiedOaimCovid;
        return $this;
    }

    public function getOccupiedO2Covid(): ?int
    {
        return $this->occupiedO2Covid;
    }

    public function setOccupiedO2Covid(?int $occupiedO2Covid): self
    {
        $this->occupiedO2Covid = $occupiedO2Covid;
        return $this;
    }

    public function getOccupiedOtherCovid(): ?int
    {
        return $this->occupiedOtherCovid;
    }

    public function setOccupiedOtherCovid(?int $occupiedOtherCovid): self
    {
        $this->occupiedOtherCovid = $occupiedOtherCovid;
        return $this;
    }
}