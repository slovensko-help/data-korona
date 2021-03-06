<?php

namespace App\Entity;

use App\Entity\Hospital;
use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use OpenApi\Annotations\Items;
use OpenApi\Annotations\Property;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
  */
class VaccinationContacts
{
    use Timestampable;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @Serializer\Exclude()
     *
     * @var integer
     */
    private $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Hospital")
     * @Serializer\Exclude()
     *
     * @var Hospital
     */
    private $hospital;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Serializer\Exclude()
     */
    private $substitutesPhones;

    /**
     * @ORM\Column(type="string", nullable=true)
     * @Serializer\Exclude()
     */
    private $substitutesEmails;

    /**
     * Webstránka s informáciami pre registráciu náhradníkov na očkovanie
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $substitutesLink;

    /**
     * Dôležitá poznámka pre registráciu náhradníkov na očkovanie
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $substitutesNote;

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

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Interné id poskytovateľa zdravotnej starostlivosti
     *
     * @Serializer\VirtualProperty()
     * @Serializer\Type("int")
     */
    public function getHospitalId(): ?int
    {
        return null === $this->hospital ? null : $this->hospital->getId();
    }

    public function getHospital(): Hospital
    {
        return $this->hospital;
    }

    public function setHospital(Hospital $hospital): self
    {
        $this->hospital = $hospital;
        return $this;
    }

    /**
     * Telefonický kontakt pre registráciu náhradníkov na očkovanie
     *
     * @Serializer\SerializedName("substitutes_phones")
     * @Serializer\VirtualProperty()
     * @Property(property="substitutes_phones", type="array", @Items(type="string"))
     */
    public function getSubstitutesPhonesArray(): array
    {
        return empty($this->substitutesPhones) ? [] : explode("\n", $this->substitutesPhones);
    }

    public function getSubstitutesPhones(): ?string
    {
        return $this->substitutesPhones;
    }

    public function setSubstitutesPhones(?string $substitutesPhones): self
    {
        $this->substitutesPhones = $substitutesPhones;
        return $this;
    }

    /**
     * Emailový kontakt pre registráciu náhradníkov na očkovanie
     *
     * @Serializer\SerializedName("substitutes_emails")
     * @Serializer\VirtualProperty()
     * @Property(property="substitutes_emails", type="array", @Items(type="string"))
     */
    public function getSubstitutesEmailsArray(): array
    {
        return empty($this->substitutesEmails) ? [] : [$this->substitutesEmails];
    }

    public function getSubstitutesEmails(): ?string
    {
        return $this->substitutesEmails;
    }

    public function setSubstitutesEmails(?string $substitutesEmails): self
    {
        $this->substitutesEmails = $substitutesEmails;
        return $this;
    }

    public function getSubstitutesLink(): ?string
    {
        return $this->substitutesLink;
    }

    public function setSubstitutesLink(?string $substitutesLink): self
    {
        $this->substitutesLink = $substitutesLink;
        return $this;
    }

    public function getSubstitutesNote(): ?string
    {
        return $this->substitutesNote;
    }

    public function setSubstitutesNote(?string $substitutesNote): self
    {
        $this->substitutesNote = $substitutesNote;
        return $this;
    }
}