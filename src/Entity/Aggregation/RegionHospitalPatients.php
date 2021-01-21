<?php

declare(strict_types=1);

namespace App\Entity\Aggregation;

use App\Entity\Traits\HospitalPatientsData;
use App\Entity\Traits\Regional;
use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegionHospitalPatientsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class RegionHospitalPatients extends AbstractData
{
    use HospitalPatientsData;
    use Timestampable;
    use Regional;

    /**
     * Interné id záznamu
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     *
     * @var int
     */
    protected $id;
}
