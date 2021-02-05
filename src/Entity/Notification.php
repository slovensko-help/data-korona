<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class Notification
{
    use Timestampable;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string", length=32)
     *
     * @var string|null
     */
    protected $id;
    /**
     * @ORM\Column(type="string", length=32)
     *
     * @var string|null
     */
    protected $contentHash;
    /**
     * @ORM\Column(type="text")
     *
     * @var string|null
     */
    protected $content;

    public function getContentHash(): ?string
    {
        return $this->contentHash;
    }

    public function setContentHash(?string $contentHash): self
    {
        $this->contentHash = $contentHash;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }
}