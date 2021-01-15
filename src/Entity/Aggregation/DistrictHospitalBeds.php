<?php

declare(strict_types=1);

namespace App\Entity\Aggregation;

use App\Entity\Traits\HospitalBedsData;
use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\DistrictHospitalBedsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class DistrictHospitalBeds extends AbstractDistrictData
{
    use HospitalBedsData;
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", options={"unsigned"=true})
     *
     * @var int
     */
    protected $id;
}
