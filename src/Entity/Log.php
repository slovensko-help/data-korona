<?php

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Log
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", options={"charset"="ascii"})
     * @var string
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=50)
     *
     * @var string
     */
    public $channel;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    public $level;

    /**
     * @ORM\Column(type="datetime_immutable")
     *
     * @var DateTimeImmutable
     */
    public $createdAt;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    public $message;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @var string|null
     */
    public $code;
}