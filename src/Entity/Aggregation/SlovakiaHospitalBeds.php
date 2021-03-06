<?php

declare(strict_types=1);

namespace App\Entity\Aggregation;

use App\Entity\Traits\HospitalBedsData;
use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\Aggregation\SlovakiaHospitalBedsRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class SlovakiaHospitalBeds extends AbstractData
{
    use HospitalBedsData;
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
