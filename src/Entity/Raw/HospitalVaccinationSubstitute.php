<?php

namespace App\Entity\Raw;

use App\Entity\Hospital;
use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
  */
class HospitalVaccinationSubstitute
{
    use Timestampable;

    /**
     * Interné id záznamu
     *
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @var int|null
     */
    private $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hospital")
     * @ORM\JoinColumn(name="hospital_id", referencedColumnName="id", nullable=true)
     * @Serializer\Exclude()
     *
     * @var Hospital|null
     */
    private $hospital;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $hospitalName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $link;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $phones;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAcceptingNewRegistrations = true;

    public function isAcceptingNewRegistrations(): bool
    {
        return $this->isAcceptingNewRegistrations;
    }

    public function setIsAcceptingNewRegistrations(bool $isAcceptingNewRegistrations): self
    {
        $this->isAcceptingNewRegistrations = $isAcceptingNewRegistrations;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getHospital(): ?Hospital
    {
        return $this->hospital;
    }

    public function setHospital(?Hospital $hospital): self
    {
        $this->hospital = $hospital;
        return $this;
    }

    public function getHospitalName(): ?string
    {
        return $this->hospitalName;
    }

    public function setHospitalName(?string $hospitalName): self
    {
        $this->hospitalName = $hospitalName;
        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;
        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(?string $note): self
    {
        $this->note = $note;
        return $this;
    }

    public function getPhones(): ?string
    {
        return $this->phones;
    }

    public function setPhones(?string $phones): self
    {
        $this->phones = $phones;
        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;
        return $this;
    }
}