<?php

declare(strict_types=1);

namespace App\Entity\Raw;

use App\Entity\Traits\Datetimeable;
use App\Entity\Traits\Publishable;
use App\Entity\Traits\Timestampable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations\Property;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Raw\NcziMorningEmailRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class NcziMorningEmail
{
    use Timestampable;
    use Publishable;
    use Datetimeable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     *
     * @var integer
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     * @Serializer\Exclude()
     * @var bool
     */
    private $isManuallyOverridden = false;

    /**
     * Čas, kedy prišiel email od NCZI
     *
     * @ORM\Column(type="datetime_immutable")
     * @Serializer\Type("DateTimeImmutable<'Y-m-d H:i:s'>")
     * @Property(example="2020-01-13 12:34:56")
     *
     * @var DateTimeImmutable
     */
    private $reportedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer|null
     */
    private $slovakiaTestsPcrPositiveDelta;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $slovakiaTestsPcrPositiveDeltaWithoutQuarantine;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionBaTestsPcrPositiveTotal;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionBbTestsPcrPositiveTotal;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionKeTestsPcrPositiveTotal;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionNrTestsPcrPositiveTotal;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionPoTestsPcrPositiveTotal;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionTnTestsPcrPositiveTotal;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionTtTestsPcrPositiveTotal;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionZaTestsPcrPositiveTotal;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionBaTestsPcrPositiveDelta;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionBbTestsPcrPositiveDelta;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionKeTestsPcrPositiveDelta;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionNrTestsPcrPositiveDelta;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionPoTestsPcrPositiveDelta;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionTnTestsPcrPositiveDelta;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionTtTestsPcrPositiveDelta;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $regionZaTestsPcrPositiveDelta;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $slovakiaTestsAgAllTotal;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $slovakiaTestsAgAllDelta;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $slovakiaTestsAgPositiveTotal;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $slovakiaTestsAgPositiveDelta;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $hospitalBedsOccupiedJisCovid;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $hospitalPatientsConfirmedCovid;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $hospitalPatientsSuspectedCovid;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer|null
     */
    private $hospitalPatientsVentilatedCovid;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer|null
     */
    private $slovakiaVaccinationAllTotal;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer|null
     */
    private $slovakiaVaccinationAllDelta;

    /**
    /**
     * @ORM\Column(type="integer", nullable=true)
     * 
     * @var integer|null
     */
    private $hospitalPatientsAllCovid;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getReportedAt(): DateTimeImmutable
    {
        return $this->reportedAt;
    }

    public function setReportedAt(DateTimeImmutable $reportedAt): self
    {
        return $this->updateDateTime($this->reportedAt, $reportedAt);
    }

    public function getSlovakiaTestsPcrPositiveDelta(): ?int
    {
        return $this->slovakiaTestsPcrPositiveDelta;
    }

    public function setSlovakiaTestsPcrPositiveDelta(?int $slovakiaTestsPcrPositiveDelta): self
    {
        $this->slovakiaTestsPcrPositiveDelta = $slovakiaTestsPcrPositiveDelta;

        return $this;
    }

    public function getSlovakiaTestsPcrPositiveDeltaWithoutQuarantine(): ?int
    {
        return $this->slovakiaTestsPcrPositiveDeltaWithoutQuarantine;
    }

    public function setSlovakiaTestsPcrPositiveDeltaWithoutQuarantine(?int $slovakiaTestsPcrPositiveDeltaWithoutQuarantine): self
    {
        $this->slovakiaTestsPcrPositiveDeltaWithoutQuarantine = $slovakiaTestsPcrPositiveDeltaWithoutQuarantine;

        return $this;
    }

    public function getRegionBaTestsPcrPositiveTotal(): ?int
    {
        return $this->regionBaTestsPcrPositiveTotal;
    }

    public function setRegionBaTestsPcrPositiveTotal(?int $regionBaTestsPcrPositiveTotal): self
    {
        $this->regionBaTestsPcrPositiveTotal = $regionBaTestsPcrPositiveTotal;

        return $this;
    }

    public function getRegionBbTestsPcrPositiveTotal(): ?int
    {
        return $this->regionBbTestsPcrPositiveTotal;
    }

    public function setRegionBbTestsPcrPositiveTotal(?int $regionBbTestsPcrPositiveTotal): self
    {
        $this->regionBbTestsPcrPositiveTotal = $regionBbTestsPcrPositiveTotal;

        return $this;
    }

    public function getRegionKeTestsPcrPositiveTotal(): ?int
    {
        return $this->regionKeTestsPcrPositiveTotal;
    }

    public function setRegionKeTestsPcrPositiveTotal(?int $regionKeTestsPcrPositiveTotal): self
    {
        $this->regionKeTestsPcrPositiveTotal = $regionKeTestsPcrPositiveTotal;

        return $this;
    }

    public function getRegionNrTestsPcrPositiveTotal(): ?int
    {
        return $this->regionNrTestsPcrPositiveTotal;
    }

    public function setRegionNrTestsPcrPositiveTotal(?int $regionNrTestsPcrPositiveTotal): self
    {
        $this->regionNrTestsPcrPositiveTotal = $regionNrTestsPcrPositiveTotal;

        return $this;
    }

    public function getRegionPoTestsPcrPositiveTotal(): ?int
    {
        return $this->regionPoTestsPcrPositiveTotal;
    }

    public function setRegionPoTestsPcrPositiveTotal(?int $regionPoTestsPcrPositiveTotal): self
    {
        $this->regionPoTestsPcrPositiveTotal = $regionPoTestsPcrPositiveTotal;

        return $this;
    }

    public function getRegionTnTestsPcrPositiveTotal(): ?int
    {
        return $this->regionTnTestsPcrPositiveTotal;
    }

    public function setRegionTnTestsPcrPositiveTotal(?int $regionTnTestsPcrPositiveTotal): self
    {
        $this->regionTnTestsPcrPositiveTotal = $regionTnTestsPcrPositiveTotal;

        return $this;
    }

    public function getRegionTtTestsPcrPositiveTotal(): ?int
    {
        return $this->regionTtTestsPcrPositiveTotal;
    }

    public function setRegionTtTestsPcrPositiveTotal(?int $regionTtTestsPcrPositiveTotal): self
    {
        $this->regionTtTestsPcrPositiveTotal = $regionTtTestsPcrPositiveTotal;

        return $this;
    }

    public function getRegionZaTestsPcrPositiveTotal(): ?int
    {
        return $this->regionZaTestsPcrPositiveTotal;
    }

    public function setRegionZaTestsPcrPositiveTotal(?int $regionZaTestsPcrPositiveTotal): self
    {
        $this->regionZaTestsPcrPositiveTotal = $regionZaTestsPcrPositiveTotal;

        return $this;
    }

    public function getRegionBaTestsPcrPositiveDelta(): ?int
    {
        return $this->regionBaTestsPcrPositiveDelta;
    }

    public function setRegionBaTestsPcrPositiveDelta(?int $regionBaTestsPcrPositiveDelta): self
    {
        $this->regionBaTestsPcrPositiveDelta = $regionBaTestsPcrPositiveDelta;

        return $this;
    }

    public function getRegionBbTestsPcrPositiveDelta(): ?int
    {
        return $this->regionBbTestsPcrPositiveDelta;
    }

    public function setRegionBbTestsPcrPositiveDelta(?int $regionBbTestsPcrPositiveDelta): self
    {
        $this->regionBbTestsPcrPositiveDelta = $regionBbTestsPcrPositiveDelta;

        return $this;
    }

    public function getRegionKeTestsPcrPositiveDelta(): ?int
    {
        return $this->regionKeTestsPcrPositiveDelta;
    }

    public function setRegionKeTestsPcrPositiveDelta(?int $regionKeTestsPcrPositiveDelta): self
    {
        $this->regionKeTestsPcrPositiveDelta = $regionKeTestsPcrPositiveDelta;

        return $this;
    }

    public function getRegionNrTestsPcrPositiveDelta(): ?int
    {
        return $this->regionNrTestsPcrPositiveDelta;
    }

    public function setRegionNrTestsPcrPositiveDelta(?int $regionNrTestsPcrPositiveDelta): self
    {
        $this->regionNrTestsPcrPositiveDelta = $regionNrTestsPcrPositiveDelta;

        return $this;
    }

    public function getRegionPoTestsPcrPositiveDelta(): ?int
    {
        return $this->regionPoTestsPcrPositiveDelta;
    }

    public function setRegionPoTestsPcrPositiveDelta(?int $regionPoTestsPcrPositiveDelta): self
    {
        $this->regionPoTestsPcrPositiveDelta = $regionPoTestsPcrPositiveDelta;

        return $this;
    }

    public function getRegionTnTestsPcrPositiveDelta(): ?int
    {
        return $this->regionTnTestsPcrPositiveDelta;
    }

    public function setRegionTnTestsPcrPositiveDelta(?int $regionTnTestsPcrPositiveDelta): self
    {
        $this->regionTnTestsPcrPositiveDelta = $regionTnTestsPcrPositiveDelta;

        return $this;
    }

    public function getRegionTtTestsPcrPositiveDelta(): ?int
    {
        return $this->regionTtTestsPcrPositiveDelta;
    }

    public function setRegionTtTestsPcrPositiveDelta(?int $regionTtTestsPcrPositiveDelta): self
    {
        $this->regionTtTestsPcrPositiveDelta = $regionTtTestsPcrPositiveDelta;

        return $this;
    }

    public function getRegionZaTestsPcrPositiveDelta(): ?int
    {
        return $this->regionZaTestsPcrPositiveDelta;
    }

    public function setRegionZaTestsPcrPositiveDelta(?int $regionZaTestsPcrPositiveDelta): self
    {
        $this->regionZaTestsPcrPositiveDelta = $regionZaTestsPcrPositiveDelta;

        return $this;
    }

    public function getSlovakiaTestsAgAllTotal(): ?int
    {
        return $this->slovakiaTestsAgAllTotal;
    }

    public function setSlovakiaTestsAgAllTotal(?int $slovakiaTestsAgAllTotal): self
    {
        $this->slovakiaTestsAgAllTotal = $slovakiaTestsAgAllTotal;

        return $this;
    }

    public function getSlovakiaTestsAgAllDelta(): ?int
    {
        return $this->slovakiaTestsAgAllDelta;
    }

    public function setSlovakiaTestsAgAllDelta(?int $slovakiaTestsAgAllDelta): self
    {
        $this->slovakiaTestsAgAllDelta = $slovakiaTestsAgAllDelta;

        return $this;
    }

    public function getSlovakiaTestsAgPositiveTotal(): ?int
    {
        return $this->slovakiaTestsAgPositiveTotal;
    }

    public function setSlovakiaTestsAgPositiveTotal(?int $slovakiaTestsAgPositiveTotal): self
    {
        $this->slovakiaTestsAgPositiveTotal = $slovakiaTestsAgPositiveTotal;

        return $this;
    }

    public function getSlovakiaTestsAgPositiveDelta(): ?int
    {
        return $this->slovakiaTestsAgPositiveDelta;
    }

    public function setSlovakiaTestsAgPositiveDelta(?int $slovakiaTestsAgPositiveDelta): self
    {
        $this->slovakiaTestsAgPositiveDelta = $slovakiaTestsAgPositiveDelta;

        return $this;
    }

    public function getHospitalBedsOccupiedJisCovid(): ?int
    {
        return $this->hospitalBedsOccupiedJisCovid;
    }

    public function setHospitalBedsOccupiedJisCovid(?int $hospitalBedsOccupiedJisCovid): self
    {
        $this->hospitalBedsOccupiedJisCovid = $hospitalBedsOccupiedJisCovid;

        return $this;
    }

    public function getHospitalPatientsConfirmedCovid(): ?int
    {
        return $this->hospitalPatientsConfirmedCovid;
    }

    public function setHospitalPatientsConfirmedCovid(?int $hospitalPatientsConfirmedCovid): self
    {
        $this->hospitalPatientsConfirmedCovid = $hospitalPatientsConfirmedCovid;

        return $this;
    }

    public function getHospitalPatientsSuspectedCovid(): ?int
    {
        return $this->hospitalPatientsSuspectedCovid;
    }

    public function setHospitalPatientsSuspectedCovid(?int $hospitalPatientsSuspectedCovid): self
    {
        $this->hospitalPatientsSuspectedCovid = $hospitalPatientsSuspectedCovid;

        return $this;
    }

    public function getHospitalPatientsVentilatedCovid(): ?int
    {
        return $this->hospitalPatientsVentilatedCovid;
    }

    public function setHospitalPatientsVentilatedCovid(?int $hospitalPatientsVentilatedCovid): self
    {
        $this->hospitalPatientsVentilatedCovid = $hospitalPatientsVentilatedCovid;

        return $this;
    }

    public function getHospitalPatientsAllCovid(): ?int
    {
        return $this->hospitalPatientsAllCovid;
    }

    public function setHospitalPatientsAllCovid(?int $hospitalPatientsAllCovid): self
    {
        $this->hospitalPatientsAllCovid = $hospitalPatientsAllCovid;

        return $this;
    }

    public function isManuallyOverridden(): bool
    {
        return $this->isManuallyOverridden;
    }

    public function setIsManuallyOverridden(bool $isManuallyOverridden): self
    {
        $this->isManuallyOverridden = $isManuallyOverridden;

        return $this;
    }

    public function getSlovakiaVaccinationAllTotal(): ?int
    {
        return $this->slovakiaVaccinationAllTotal;
    }

    public function setSlovakiaVaccinationAllTotal(?int $slovakiaVaccinationAllTotal): self
    {
        $this->slovakiaVaccinationAllTotal = $slovakiaVaccinationAllTotal;

        return $this;
    }

    public function getSlovakiaVaccinationAllDelta(): ?int
    {
        return $this->slovakiaVaccinationAllDelta;
    }

    public function setSlovakiaVaccinationAllDelta(?int $slovakiaVaccinationAllDelta): self
    {
        $this->slovakiaVaccinationAllDelta = $slovakiaVaccinationAllDelta;

        return $this;
    }
}