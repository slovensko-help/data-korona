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
class HospitalPatients
{
    use HospitalPatientsData;
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
}
