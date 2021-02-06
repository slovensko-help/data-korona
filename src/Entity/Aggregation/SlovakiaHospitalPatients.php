<?php

declare(strict_types=1);

namespace App\Entity\Aggregation;

use App\Entity\Traits\HospitalPatientsData;
use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Aggregation\SlovakiaHospitalPatientsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class SlovakiaHospitalPatients extends AbstractData
{
    use HospitalPatientsData;
    use Timestampable;

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
