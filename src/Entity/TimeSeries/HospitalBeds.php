<?php

declare(strict_types=1);

namespace App\Entity\TimeSeries;

use App\Entity\Hospital;
use App\Entity\Traits\Datetimeable;
use App\Entity\Traits\HospitalBedsData;
use App\Entity\Traits\Publishable;
use App\Entity\Traits\Timestampable;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations\Property;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 *
 * data source: https://raw.githubusercontent.com/Institut-Zdravotnych-Analyz/covid19-data/main/OpenData_Slovakia_Covid_Hospital_Details.csv
 */
class HospitalBeds
{
    use Datetimeable;
    use Timestampable;
    use Publishable;
    use HospitalBedsData;

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
     * @ORM\ManyToOne(targetEntity="App\Entity\Hospital")
     *
     * @Serializer\Exclude()
     *
     * @var Hospital
     */
    private $hospital;

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

    public function setHospital(Hospital $hospital): self
    {
        $this->hospital = $hospital;
        return $this;
    }
}
