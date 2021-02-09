<?php

namespace App\Repository\TimeSeries;


use App\Entity\Aggregation\SlovakiaVaccinations;
use App\Entity\Raw\PowerBiVaccinations;
use App\Entity\Region;
use App\Entity\TimeSeries\Vaccinations;
use App\Repository\ServiceEntityRepository;
use App\Service\Vaccination;
use Doctrine\Persistence\ManagerRegistry;
use Generator;

class VaccinationsRepository extends ServiceEntityRepository
{
    /** @var Vaccination */
    private $vaccination;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vaccinations::class);
    }

    public function vaccinationsFromRawEntities(): Generator
    {
        foreach ($this->getEntityManager()->getRepository(PowerBiVaccinations::class)->findAll() as $entity) {
            yield $entity;
        }
    }

    public function slovakiaVaccinations(): array
    {
        return $this->vaccination->dailyStats();
    }

    public function vaccinationsEntities(): callable
    {
        return function (PowerBiVaccinations $_) {
            yield 'id' => function (Vaccinations $vaccinations) use ($_) {
                return $vaccinations
                    ->setRegion($_->getRegion())
                    ->setVaccine($_->getVaccine())
                    ->setId($_->getCode())
                    ->setPublishedOn($_->getPublishedOn())
                    ->setDose1Count($_->getDose1Count())
                    ->setDose2Count($_->getDose1Count());
            };
        };
    }

    public function slovakiaVaccinationsEntities(): callable
    {
        return function (array $_) {
            yield 'id' => function (SlovakiaVaccinations $slovakiaVaccinations) use ($_) {
//                if (null === $_['nczi'])
//                {
//
//                }
//                if (null === $_['power_bi'] && null === $_['iza']) {
//
//                }
//
//                return $slovakiaVaccinations
//                    ->setId($_->getCode())
//                    ->setPublishedOn($_->getPublishedOn())
//                    ->setDose1Count($_->getDose1Count())
//                    ->setDose2Count($_->getDose1Count());
            };
        };
    }

    /**
     * @required
     * @param Vaccination $vaccination
     */
    public function setVaccination(Vaccination $vaccination): void
    {
        $this->vaccination = $vaccination;
    }
}