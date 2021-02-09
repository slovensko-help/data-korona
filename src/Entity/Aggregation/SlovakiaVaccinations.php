<?php

declare(strict_types=1);

namespace App\Entity\Aggregation;

use App\Entity\Traits\Publishable;
use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class SlovakiaVaccinations
{
    use Timestampable;
    use Publishable;

    /**
     * Interné id záznamu
     *
     * @ORM\Id()
     * @ORM\Column(type="string", options={"charset"="ascii"})
     *
     * @var int
     */
    protected $id;

    /**
     * Počet podaných prvých dávok vakcín pre daný deň
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $dose1Count;

    /**
     * Počet podaných druhých dávok vakcín pre daný deň
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $dose2Count;

    /**
     * Súčet podaných prvých dávok vakcín od začiatku vakcinácie do daného dňa
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $dose1Sum;

    /**
     * Súčet podaných druhých dávok vakcín od začiatku vakcinácie do daného dňa
     *
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var integer
     */
    private $dose2Sum;
}
