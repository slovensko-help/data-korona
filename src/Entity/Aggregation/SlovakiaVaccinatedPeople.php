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
    private $fullyVaccinatedPatientsRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $partiallyVaccinatedPatientsRate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     *
     * @var int
     * @Serializer\Exclude()
     */
    private $unknownDoseButVaccinatedPatientsRate;

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

    public function getUnvaccinatedAgPositivesRate(): int
    {
        return $this->unvaccinatedAgPositivesRate;
    }

    public function getFullyVaccinatedAgPositivesRate(): int
    {
        return $this->fullyVaccinatedAgPositivesRate;
    }

    public function getPartiallyVaccinatedAgPositivesRate(): int
    {
        return $this->partiallyVaccinatedAgPositivesRate;
    }

    public function getUnknownAgPositivesRate(): int
    {
        return $this->unknownAgPositivesRate;
    }

    public function getUnvaccinatedAgNegativesRate(): int
    {
        return $this->unvaccinatedAgNegativesRate;
    }

    public function getFullyVaccinatedAgNegativesRate(): int
    {
        return $this->fullyVaccinatedAgNegativesRate;
    }

    public function getPartiallyVaccinatedAgNegativesRate(): int
    {
        return $this->partiallyVaccinatedAgNegativesRate;
    }

    public function getUnknownAgNegativesRate(): int
    {
        return $this->unknownAgNegativesRate;
    }

    public function getUnvaccinatedPcrPositivesRate(): int
    {
        return $this->unvaccinatedPcrPositivesRate;
    }

    public function getFullyVaccinatedPcrPositivesRate(): int
    {
        return $this->fullyVaccinatedPcrPositivesRate;
    }

    public function getPartiallyVaccinatedPcrPositivesRate(): int
    {
        return $this->partiallyVaccinatedPcrPositivesRate;
    }

    public function getUnknownPcrPositivesRate(): int
    {
        return $this->unknownPcrPositivesRate;
    }

    public function getUnvaccinatedPcrNegativesRate(): int
    {
        return $this->unvaccinatedPcrNegativesRate;
    }

    public function getFullyVaccinatedPcrNegativesRate(): int
    {
        return $this->fullyVaccinatedPcrNegativesRate;
    }

    public function getPartiallyVaccinatedPcrNegativesRate(): int
    {
        return $this->partiallyVaccinatedPcrNegativesRate;
    }

    public function getUnknownPcrNegativesRate(): int
    {
        return $this->unknownPcrNegativesRate;
    }

    public function getUnvaccinatedPatientsRate(): int
    {
        return $this->unvaccinatedPatientsRate;
    }

    public function getUnknownPatientsRate(): int
    {
        return $this->unknownPatientsRate;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function hasHospitalizedPatients(): bool
    {
        return !(null === $this->fullyVaccinatedPatientsRate &&
            null === $this->partiallyVaccinatedPatientsRate &&
            null === $this->unvaccinatedPatientsRate &&
            null === $this->unknownPatientsRate);
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function hasTests(): bool
    {
        foreach ($this->getTests() as $group) {
            foreach ($group as $item) {
                if (null !== $item) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getHospitalizedPatients(): array
    {
        $vaccinated = $this->sum(
            $this->sum($this->fullyVaccinatedPatientsRate, $this->partiallyVaccinatedPatientsRate),
            $this->unknownDoseButVaccinatedPatientsRate);

        return [
            'vaccinated' => $this->percentage($vaccinated),
            'fully_vaccinated' => $this->percentage($this->fullyVaccinatedPatientsRate),
            'partially_vaccinated' => $this->percentage($this->partiallyVaccinatedPatientsRate),
            'unknown_dose_but_vaccinated' => $this->percentage($this->unknownDoseButVaccinatedPatientsRate),
            'unvaccinated' => $this->percentage($this->unvaccinatedPatientsRate),
            'unknown' => $this->percentage($this->unknownPatientsRate),
        ];
    }

    /**
     * @Serializer\VirtualProperty()
     */
    public function getTests(): array
    {
        return [
            'positive_ag' => [
                'vaccinated' => $this->percentage($this->sum($this->fullyVaccinatedAgPositivesRate, $this->partiallyVaccinatedAgPositivesRate)),
                'fully_vaccinated' => $this->percentage($this->fullyVaccinatedAgPositivesRate),
                'partially_vaccinated' => $this->percentage($this->partiallyVaccinatedAgPositivesRate),
                'unvaccinated' => $this->percentage($this->unvaccinatedAgPositivesRate),
                'unknown' => $this->percentage($this->unknownAgPositivesRate),
            ],
            'negative_ag' => [
                'vaccinated' => $this->percentage($this->sum($this->fullyVaccinatedAgNegativesRate, $this->partiallyVaccinatedAgNegativesRate)),
                'fully_vaccinated' => $this->percentage($this->fullyVaccinatedAgNegativesRate),
                'partially_vaccinated' => $this->percentage($this->partiallyVaccinatedAgNegativesRate),
                'unvaccinated' => $this->percentage($this->unvaccinatedAgNegativesRate),
                'unknown' => $this->percentage($this->unknownAgNegativesRate),
            ],
            'positive_pcr' => [
                'vaccinated' => $this->percentage($this->sum($this->fullyVaccinatedPcrPositivesRate, $this->partiallyVaccinatedPcrPositivesRate)),
                'fully_vaccinated' => $this->percentage($this->fullyVaccinatedPcrPositivesRate),
                'partially_vaccinated' => $this->percentage($this->partiallyVaccinatedPcrPositivesRate),
                'unvaccinated' => $this->percentage($this->unvaccinatedPcrPositivesRate),
                'unknown' => $this->percentage($this->unknownPcrPositivesRate),
            ],
            'negative_pcr' => [
                'vaccinated' => $this->percentage($this->sum($this->fullyVaccinatedPcrNegativesRate, $this->partiallyVaccinatedPcrNegativesRate)),
                'fully_vaccinated' => $this->percentage($this->fullyVaccinatedPcrNegativesRate),
                'partially_vaccinated' => $this->percentage($this->partiallyVaccinatedPcrNegativesRate),
                'unvaccinated' => $this->percentage($this->unvaccinatedPcrNegativesRate),
                'unknown' => $this->percentage($this->unknownPcrNegativesRate),
            ],
        ];
    }

    private function sum(?int $a, ?int $b): ?int
    {
        if (null === $a && null === $b) {
            return null;
        }

        return ($a ?? 0) + ($b ?? 0);
    }

    private function percentage(?int $value): ?float
    {
        return null === $value ? $value : $value / 100;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function setUnvaccinatedAgPositivesRate(int $unvaccinatedAgPositivesRate): self
    {
        $this->unvaccinatedAgPositivesRate = $unvaccinatedAgPositivesRate;
        return $this;
    }

    public function setFullyVaccinatedAgPositivesRate(int $fullyVaccinatedAgPositivesRate): self
    {
        $this->fullyVaccinatedAgPositivesRate = $fullyVaccinatedAgPositivesRate;
        return $this;
    }

    public function setPartiallyVaccinatedAgPositivesRate(int $partiallyVaccinatedAgPositivesRate): self
    {
        $this->partiallyVaccinatedAgPositivesRate = $partiallyVaccinatedAgPositivesRate;
        return $this;
    }

    public function setUnknownAgPositivesRate(int $unknownAgPositivesRate): self
    {
        $this->unknownAgPositivesRate = $unknownAgPositivesRate;
        return $this;
    }

    public function setUnvaccinatedAgNegativesRate(int $unvaccinatedAgNegativesRate): self
    {
        $this->unvaccinatedAgNegativesRate = $unvaccinatedAgNegativesRate;
        return $this;
    }

    public function setFullyVaccinatedAgNegativesRate(int $fullyVaccinatedAgNegativesRate): self
    {
        $this->fullyVaccinatedAgNegativesRate = $fullyVaccinatedAgNegativesRate;
        return $this;
    }

    public function setPartiallyVaccinatedAgNegativesRate(int $partiallyVaccinatedAgNegativesRate): self
    {
        $this->partiallyVaccinatedAgNegativesRate = $partiallyVaccinatedAgNegativesRate;
        return $this;
    }

    public function setUnknownAgNegativesRate(int $unknownAgNegativesRate): self
    {
        $this->unknownAgNegativesRate = $unknownAgNegativesRate;
        return $this;
    }

    public function setUnvaccinatedPcrPositivesRate(int $unvaccinatedPcrPositivesRate): self
    {
        $this->unvaccinatedPcrPositivesRate = $unvaccinatedPcrPositivesRate;
        return $this;
    }

    public function setFullyVaccinatedPcrPositivesRate(int $fullyVaccinatedPcrPositivesRate): self
    {
        $this->fullyVaccinatedPcrPositivesRate = $fullyVaccinatedPcrPositivesRate;
        return $this;
    }

    public function setPartiallyVaccinatedPcrPositivesRate(int $partiallyVaccinatedPcrPositivesRate): self
    {
        $this->partiallyVaccinatedPcrPositivesRate = $partiallyVaccinatedPcrPositivesRate;
        return $this;
    }

    public function setUnknownPcrPositivesRate(int $unknownPcrPositivesRate): self
    {
        $this->unknownPcrPositivesRate = $unknownPcrPositivesRate;
        return $this;
    }

    public function setUnvaccinatedPcrNegativesRate(int $unvaccinatedPcrNegativesRate): self
    {
        $this->unvaccinatedPcrNegativesRate = $unvaccinatedPcrNegativesRate;
        return $this;
    }

    public function setFullyVaccinatedPcrNegativesRate(int $fullyVaccinatedPcrNegativesRate): self
    {
        $this->fullyVaccinatedPcrNegativesRate = $fullyVaccinatedPcrNegativesRate;
        return $this;
    }

    public function setPartiallyVaccinatedPcrNegativesRate(int $partiallyVaccinatedPcrNegativesRate): self
    {
        $this->partiallyVaccinatedPcrNegativesRate = $partiallyVaccinatedPcrNegativesRate;
        return $this;
    }

    public function setUnknownPcrNegativesRate(int $unknownPcrNegativesRate): self
    {
        $this->unknownPcrNegativesRate = $unknownPcrNegativesRate;
        return $this;
    }

    public function setUnvaccinatedPatientsRate(int $unvaccinatedPatientsRate): self
    {
        $this->unvaccinatedPatientsRate = $unvaccinatedPatientsRate;
        return $this;
    }

    public function setUnknownPatientsRate(int $unknownPatientsRate): self
    {
        $this->unknownPatientsRate = $unknownPatientsRate;
        return $this;
    }

    public function getFullyVaccinatedPatientsRate(): int
    {
        return $this->fullyVaccinatedPatientsRate;
    }

    public function setFullyVaccinatedPatientsRate(int $fullyVaccinatedPatientsRate): self
    {
        $this->fullyVaccinatedPatientsRate = $fullyVaccinatedPatientsRate;
        return $this;
    }

    public function getPartiallyVaccinatedPatientsRate(): int
    {
        return $this->partiallyVaccinatedPatientsRate;
    }

    public function setPartiallyVaccinatedPatientsRate(int $partiallyVaccinatedPatientsRate): self
    {
        $this->partiallyVaccinatedPatientsRate = $partiallyVaccinatedPatientsRate;
        return $this;
    }

    public function getUnknownDoseButVaccinatedPatientsRate(): int
    {
        return $this->unknownDoseButVaccinatedPatientsRate;
    }

    public function setUnknownDoseButVaccinatedPatientsRate(int $unknownDoseButVaccinatedPatientsRate): self
    {
        $this->unknownDoseButVaccinatedPatientsRate = $unknownDoseButVaccinatedPatientsRate;
        return $this;
    }
}
