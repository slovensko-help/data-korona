<?php

declare(strict_types=1);

namespace App\Entity\Aggregation;

use App\Entity\Traits\HospitalPatientsData;
use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DistrictHospitalPatientsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class DistrictHospitalPatients extends AbstractDistrictData
{
    use HospitalPatientsData;
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     *
     * @var int
     */
    protected $id;
}
