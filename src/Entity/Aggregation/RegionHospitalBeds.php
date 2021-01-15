<?php

declare(strict_types=1);

namespace App\Entity\Aggregation;

use App\Entity\Traits\HospitalBedsData;
use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\RegionHospitalBedsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class RegionHospitalBeds extends AbstractRegionData
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
