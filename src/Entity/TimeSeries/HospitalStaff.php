<?php

declare(strict_types=1);

namespace App\Entity\TimeSeries;

use App\Entity\Hospital;
use App\Entity\Traits\Datetimeable;
use App\Entity\Traits\HospitalPatientsData;
use App\Entity\Traits\Publishable;
use App\Entity\Traits\Timestampable;
use DateTimeImmutable;
use DH\Auditor\Provider\Doctrine\Auditing\Annotation\Auditable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations\Property;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 * @Auditable()
 *
 * data source: https://raw.githubusercontent.com/Institut-Zdravotnych-Analyz/covid19-data/main/OpenData_Slovakia_Covid_Hospital_Details.csv
 */
class HospitalStaff
{
    use Timestampable;
    use Datetimeable;
    use Publishable;

    /**
     * Interné id záznamu
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned"=true})
     *
     * @var int
     */
    private $id;

    /**
     * Čas, kedy záznam reportovala nemocnica
     *
     * @ORM\Column(type="datetime_immutable")
     * @Serializer\Type("DateTimeImmutable<'Y-m-d H:i:s'>")
     * @Property(example="2020-01-13 12:34:56")
     *
     * @var DateTimeImmutable
     */
    private $reportedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hospital")
     *
     * @Serializer\Exclude()
     *
     * @var Hospital
     */
    private $hospital;

    /**
     * Percentuálny podiel doktorov, ktorí majú potvrdený COVID alebo sú v karanténe z počtu všetkých doktorov
     *
     * @ORM\Column(type="float", nullable=true)
     *
     * source: PERSONAL_LEKAR_PERC_PN
     */
    private $outOfWorkRatioDoctor;

    /**
     * Percentuálny podiel sestier, ktoré majú potvrdený COVID alebo sú v karanténe z počtu všetkých sestier
     *
     * @ORM\Column(type="float", nullable=true)
     *
     * source: PERSONAL_SESTRA_PERC_PN
     */
    private $outOfWorkRatioNurse;

    /**
     * Percentuálny podiel iných zdravotníckych pracovníkov, ktorí majú potvrdený COVID alebo sú v karanténe z počtu všetkých iných zdravotníckych pracovníkov
     *
     * @ORM\Column(type="float", nullable=true)
     *
     * source: PERSONAL_OSTATNI_PERC_PN
     */
    private $outOfWorkRatioOther;

    public function getId(): int
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

    public function getHospital(): Hospital
    {
        return $this->hospital;
    }

    public function setHospital(Hospital $hospital): self
    {
        $this->hospital = $hospital;
        return $this;
    }

    /**
     * Interné id nemocnice z /api/hospitals
     *
     * @Serializer\VirtualProperty()
     * @Serializer\Type("int")
     */
    public function getHospitalId(): int
    {
        return $this->hospital->getId();
    }

    public function getOutOfWorkRatioDoctor(): ?float
    {
        return $this->outOfWorkRatioDoctor;
    }

    public function setOutOfWorkRatioDoctor(?float $outOfWorkRatioDoctor): self
    {
        $this->outOfWorkRatioDoctor = $outOfWorkRatioDoctor;
        return $this;
    }

    public function getOutOfWorkRatioNurse(): ?float
    {
        return $this->outOfWorkRatioNurse;
    }

    public function setOutOfWorkRatioNurse(?float $outOfWorkRatioNurse): self
    {
        $this->outOfWorkRatioNurse = $outOfWorkRatioNurse;
        return $this;
    }

    public function getOutOfWorkRatioOther(): ?float
    {
        return $this->outOfWorkRatioOther;
    }

    public function setOutOfWorkRatioOther(?float $outOfWorkRatioOther): self
    {
        $this->outOfWorkRatioOther = $outOfWorkRatioOther;
        return $this;
    }
}
