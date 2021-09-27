<?php

declare(strict_types=1);

namespace App\Entity\Aggregation;

use App\Entity\Traits\Publishable;
use App\Entity\Traits\Timestampable;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity()
 * @ORM\HasLifecycleCallbacks()
 */
class SlovakiaVaccinatedPeople
{
    use Timestampable;
    use Publishable;

    /**
     * Interné id záznamu
     *
     * @ORM\Id()
     * @ORM\Column(type="string", options={"charset"="ascii"})
     *
     * @var string
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $unvaccinatedAgPositivesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $fullyVaccinatedAgPositivesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $partiallyVaccinatedAgPositivesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $unknownAgPositivesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $unvaccinatedAgNegativesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $fullyVaccinatedAgNegativesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $partiallyVaccinatedAgNegativesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $unknownAgNegativesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $unvaccinatedPcrPositivesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $fullyVaccinatedPcrPositivesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $partiallyVaccinatedPcrPositivesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $unknownPcrPositivesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $unvaccinatedPcrNegativesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $fullyVaccinatedPcrNegativesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $partiallyVaccinatedPcrNegativesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $unknownPcrNegativesRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $vaccinatedPatientsRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $unvaccinatedPatientsRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $unknownPatientsRate;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getUnvaccinatedAgPositivesRate(): int
    {
        return $this->unvaccinatedAgPositivesRate;
    }

    public function setUnvaccinatedAgPositivesRate(int $unvaccinatedAgPositivesRate): self
    {
        $this->unvaccinatedAgPositivesRate = $unvaccinatedAgPositivesRate;
        return $this;
    }

    public function getFullyVaccinatedAgPositivesRate(): int
    {
        return $this->fullyVaccinatedAgPositivesRate;
    }

    public function setFullyVaccinatedAgPositivesRate(int $fullyVaccinatedAgPositivesRate): self
    {
        $this->fullyVaccinatedAgPositivesRate = $fullyVaccinatedAgPositivesRate;
        return $this;
    }

    public function getPartiallyVaccinatedAgPositivesRate(): int
    {
        return $this->partiallyVaccinatedAgPositivesRate;
    }

    public function setPartiallyVaccinatedAgPositivesRate(int $partiallyVaccinatedAgPositivesRate): self
    {
        $this->partiallyVaccinatedAgPositivesRate = $partiallyVaccinatedAgPositivesRate;
        return $this;
    }

    public function getUnknownAgPositivesRate(): int
    {
        return $this->unknownAgPositivesRate;
    }

    public function setUnknownAgPositivesRate(int $unknownAgPositivesRate): self
    {
        $this->unknownAgPositivesRate = $unknownAgPositivesRate;
        return $this;
    }

    public function getUnvaccinatedAgNegativesRate(): int
    {
        return $this->unvaccinatedAgNegativesRate;
    }

    public function setUnvaccinatedAgNegativesRate(int $unvaccinatedAgNegativesRate): self
    {
        $this->unvaccinatedAgNegativesRate = $unvaccinatedAgNegativesRate;
        return $this;
    }

    public function getFullyVaccinatedAgNegativesRate(): int
    {
        return $this->fullyVaccinatedAgNegativesRate;
    }

    public function setFullyVaccinatedAgNegativesRate(int $fullyVaccinatedAgNegativesRate): self
    {
        $this->fullyVaccinatedAgNegativesRate = $fullyVaccinatedAgNegativesRate;
        return $this;
    }

    public function getPartiallyVaccinatedAgNegativesRate(): int
    {
        return $this->partiallyVaccinatedAgNegativesRate;
    }

    public function setPartiallyVaccinatedAgNegativesRate(int $partiallyVaccinatedAgNegativesRate): self
    {
        $this->partiallyVaccinatedAgNegativesRate = $partiallyVaccinatedAgNegativesRate;
        return $this;
    }

    public function getUnknownAgNegativesRate(): int
    {
        return $this->unknownAgNegativesRate;
    }

    public function setUnknownAgNegativesRate(int $unknownAgNegativesRate): self
    {
        $this->unknownAgNegativesRate = $unknownAgNegativesRate;
        return $this;
    }

    public function getUnvaccinatedPcrPositivesRate(): int
    {
        return $this->unvaccinatedPcrPositivesRate;
    }

    public function setUnvaccinatedPcrPositivesRate(int $unvaccinatedPcrPositivesRate): self
    {
        $this->unvaccinatedPcrPositivesRate = $unvaccinatedPcrPositivesRate;
        return $this;
    }

    public function getFullyVaccinatedPcrPositivesRate(): int
    {
        return $this->fullyVaccinatedPcrPositivesRate;
    }

    public function setFullyVaccinatedPcrPositivesRate(int $fullyVaccinatedPcrPositivesRate): self
    {
        $this->fullyVaccinatedPcrPositivesRate = $fullyVaccinatedPcrPositivesRate;
        return $this;
    }

    public function getPartiallyVaccinatedPcrPositivesRate(): int
    {
        return $this->partiallyVaccinatedPcrPositivesRate;
    }

    public function setPartiallyVaccinatedPcrPositivesRate(int $partiallyVaccinatedPcrPositivesRate): self
    {
        $this->partiallyVaccinatedPcrPositivesRate = $partiallyVaccinatedPcrPositivesRate;
        return $this;
    }

    public function getUnknownPcrPositivesRate(): int
    {
        return $this->unknownPcrPositivesRate;
    }

    public function setUnknownPcrPositivesRate(int $unknownPcrPositivesRate): self
    {
        $this->unknownPcrPositivesRate = $unknownPcrPositivesRate;
        return $this;
    }

    public function getUnvaccinatedPcrNegativesRate(): int
    {
        return $this->unvaccinatedPcrNegativesRate;
    }

    public function setUnvaccinatedPcrNegativesRate(int $unvaccinatedPcrNegativesRate): self
    {
        $this->unvaccinatedPcrNegativesRate = $unvaccinatedPcrNegativesRate;
        return $this;
    }

    public function getFullyVaccinatedPcrNegativesRate(): int
    {
        return $this->fullyVaccinatedPcrNegativesRate;
    }

    public function setFullyVaccinatedPcrNegativesRate(int $fullyVaccinatedPcrNegativesRate): self
    {
        $this->fullyVaccinatedPcrNegativesRate = $fullyVaccinatedPcrNegativesRate;
        return $this;
    }

    public function getPartiallyVaccinatedPcrNegativesRate(): int
    {
        return $this->partiallyVaccinatedPcrNegativesRate;
    }

    public function setPartiallyVaccinatedPcrNegativesRate(int $partiallyVaccinatedPcrNegativesRate): self
    {
        $this->partiallyVaccinatedPcrNegativesRate = $partiallyVaccinatedPcrNegativesRate;
        return $this;
    }

    public function getUnknownPcrNegativesRate(): int
    {
        return $this->unknownPcrNegativesRate;
    }

    public function setUnknownPcrNegativesRate(int $unknownPcrNegativesRate): self
    {
        $this->unknownPcrNegativesRate = $unknownPcrNegativesRate;
        return $this;
    }

    public function getVaccinatedPatientsRate(): int
    {
        return $this->vaccinatedPatientsRate;
    }

    public function setVaccinatedPatientsRate(int $vaccinatedPatientsRate): self
    {
        $this->vaccinatedPatientsRate = $vaccinatedPatientsRate;
        return $this;
    }

    public function getUnvaccinatedPatientsRate(): int
    {
        return $this->unvaccinatedPatientsRate;
    }

    public function setUnvaccinatedPatientsRate(int $unvaccinatedPatientsRate): self
    {
        $this->unvaccinatedPatientsRate = $unvaccinatedPatientsRate;
        return $this;
    }

    public function getUnknownPatientsRate(): int
    {
        return $this->unknownPatientsRate;
    }

    public function setUnknownPatientsRate(int $unknownPatientsRate): self
    {
        $this->unknownPatientsRate = $unknownPatientsRate;
        return $this;
    }

    /**
     * Interné id kraja z /api/regions
     *
     * @Serializer\VirtualProperty()
     */
    public function getHospitalizedPatients(): array
    {
        return [
            'vaccinated' => $this->vaccinatedPatientsRate / 100,
            'unvaccinated' => $this->unvaccinatedPatientsRate / 100,
            'unknown' => $this->unknownPatientsRate / 100,
        ];
    }

    /**
     * Interné id kraja z /api/regions
     *
     * @Serializer\VirtualProperty()
     */
    public function getTests(): array
    {
        return [
            'positive_ag' => [
                'vaccinated' => ($this->fullyVaccinatedAgPositivesRate + $this->partiallyVaccinatedAgPositivesRate) / 100,
                'fully_vaccinated' => $this->fullyVaccinatedAgPositivesRate / 100,
                'partially_vaccinated' => $this->partiallyVaccinatedAgPositivesRate / 100,
                'unvaccinated' => $this->unvaccinatedAgPositivesRate / 100,
                'unknown' => $this->unknownAgPositivesRate / 100,
            ],
            'negative_ag' => [
                'vaccinated' => ($this->fullyVaccinatedAgNegativesRate + $this->partiallyVaccinatedAgNegativesRate) / 100,
                'fully_vaccinated' => $this->fullyVaccinatedAgNegativesRate / 100,
                'partially_vaccinated' => $this->partiallyVaccinatedAgNegativesRate / 100,
                'unvaccinated' => $this->unvaccinatedAgNegativesRate / 100,
                'unknown' => $this->unknownAgNegativesRate / 100,
            ],
            'positive_pcr' => [
                'vaccinated' => ($this->fullyVaccinatedPcrPositivesRate + $this->partiallyVaccinatedPcrPositivesRate) / 100,
                'fully_vaccinated' => $this->fullyVaccinatedPcrPositivesRate / 100,
                'partially_vaccinated' => $this->partiallyVaccinatedPcrPositivesRate / 100,
                'unvaccinated' => $this->unvaccinatedPcrPositivesRate / 100,
                'unknown' => $this->unknownPcrPositivesRate / 100,
            ],
            'negative_pcr' => [
                'vaccinated' => ($this->fullyVaccinatedPcrNegativesRate + $this->partiallyVaccinatedPcrNegativesRate) / 100,
                'fully_vaccinated' => $this->fullyVaccinatedPcrNegativesRate / 100,
                'partially_vaccinated' => $this->partiallyVaccinatedPcrNegativesRate / 100,
                'unvaccinated' => $this->unvaccinatedPcrNegativesRate / 100,
                'unknown' => $this->unknownPcrNegativesRate / 100,
            ],
        ];
    }
}
