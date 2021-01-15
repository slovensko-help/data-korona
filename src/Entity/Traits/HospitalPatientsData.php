<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait HospitalPatientsData {

    /**
     * Počet pacientov, ktorí majú COVID a sú na pľúcnej ventilácii
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * source: POSTELE_COVID_PL
     */
    private $ventilatedCovid;

    /**
     * Počet pacientov, ktorí nemajú potvrdený COVID a nemajú ani podozrenie na COVID
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * source: ZAR_OBSADENE
     */
    private $nonCovid;

    /**
     * Počet pacientov, ktorí majú potvrdený COVID
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * source: ZAR_COVID
     */
    private $confirmedCovid;

    /**
     * Počet pacientov, ktorí majú podozrenie na COVID
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * source: ZAR_COVID_HYPOT
     */
    private $suspectedCovid;

    public function getVentilatedCovid(): ?int
    {
        return $this->ventilatedCovid;
    }

    public function setVentilatedCovid(?int $ventilatedCovid): self
    {
        $this->ventilatedCovid = $ventilatedCovid;
        return $this;
    }

    public function getNonCovid(): ?int
    {
        return $this->nonCovid;
    }

    public function setNonCovid(?int $nonCovid): self
    {
        $this->nonCovid = $nonCovid;
        return $this;
    }

    public function getConfirmedCovid(): ?int
    {
        return $this->confirmedCovid;
    }

    public function setConfirmedCovid(?int $confirmedCovid): self
    {
        $this->confirmedCovid = $confirmedCovid;
        return $this;
    }

    public function getSuspectedCovid(): ?int
    {
        return $this->suspectedCovid;
    }

    public function setSuspectedCovid(?int $suspectedCovid): self
    {
        $this->suspectedCovid = $suspectedCovid;
        return $this;
    }
}