<?php

namespace App\Repository\TimeSeries;


use App\Entity\Aggregation\RegionVaccinations;
use App\Entity\Aggregation\SlovakiaVaccinations;
use App\Entity\Raw\PowerBiVaccinations;
use App\Entity\Region;
use App\Entity\TimeSeries\Vaccinations;
use App\Repository\ServiceEntityRepository;
use App\Service\Vaccination;
use App\Tool\DateTime;
use App\Tool\Id;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Generator;

class VaccinationsRepository extends ServiceEntityRepository
{
    /** @var Vaccination */
    private $vaccination;

    /** @var DateTimeImmutable */
    private $today;

    /** @var DateTimeImmutable */
    private $yesterday;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vaccinations::class);

        $this->today = (new DateTimeImmutable('now'))->setTime(0, 0);
        $this->yesterday = (new DateTimeImmutable('yesterday'))->setTime(0, 0);
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

    public function regionVaccinations(): array
    {
        return $this->vaccination->regionalDailyStats();
    }

    public function vaccinationsEntities(): callable
    {
        return function (PowerBiVaccinations $_) {

            yield 'id:readonly' => function (SlovakiaVaccinations $slovakiaVaccinations) use ($_) {
                return $slovakiaVaccinations
                    ->setId($_->getPublishedOn()->format('Ymd'));
            };

            yield 'id' => function (Vaccinations $vaccinations, ?SlovakiaVaccinations $slovakiaVaccinations) use ($_) {
                if (null === $slovakiaVaccinations) {
                    return null;
                }
                $uow = $this->getEntityManager()->getUnitOfWork();
                if ($uow->getEntityState($_) !== 1) {
                    dump([$_, $uow->getEntityState($_->getVaccine()), $uow->getEntityState($_->getRegion())]);
                    die;
                }
                return $vaccinations
                    ->setId($_->getCode())
                    ->setRegion($_->getRegion())
                    ->setVaccine($_->getVaccine())
                    ->setPublishedOn($_->getPublishedOn())
                    ->setDose1Count($_->getDose1Count())
                    ->setDose2Count($_->getDose2Count());
            };
        };
    }

    public function slovakiaVaccinationsEntities(): callable
    {
        return function (array $_) {
            yield 'id' => function (SlovakiaVaccinations $slovakiaVaccinations) use ($_) {
                $update = null;

                if (null !== $_['nczi']) {
                    $publishedOn = DateTime::dateTimeFromString($_['nczi']['published_on'], 'Y-m-d', true);

                    // yesterday's update is always taken from nczi (if it exists)
                    if ($publishedOn == $this->yesterday) {
                        $update = $_['nczi'];
                    }
                }

                // if it is not yesterday's update from nczi then take first available update in this order: power_bi, iza, nczi
                if (null === $update) {
                    if (null !== $_['power_bi']) {
                        $update = $_['power_bi'];
                    } elseif (null !== $_['iza']) {
                        $update = $_['iza'];
                    } elseif (null !== $_['nczi']) {
                        $update = $_['nczi'];
                    }

                    // nothing :(
                    if (null === $update) {
                        return null;
                    }

                    $publishedOn = DateTime::dateTimeFromString($update['published_on'], 'Y-m-d', true);

                    // today's updates are not reliable - skip :/
                    // yesterday's updates from power_bi or iza could be only partial so we must skip too :/
                    if ($publishedOn >= $this->yesterday) {
                        return null;
                    }
                }

                return $slovakiaVaccinations
                    ->setId($publishedOn->format('Ymd'))
                    ->setPublishedOn($publishedOn)
                    ->setDose1Count(isset($update['dose1_count']) ? ((int)$update['dose1_count']) : null)
                    ->setDose2Count(isset($update['dose2_count']) ? ((int)$update['dose2_count']) : null)
                    ->setDose1Sum((int)$update['dose1_sum'])
                    ->setDose2Sum((int)$update['dose2_sum']);
            };
        };
    }

    public function regionVaccinationsEntities(): callable
    {
        return function (array $_) {
            yield 'id' => function (Region $region) use ($_) {
                return $region
                    ->setId((int)$_['region_id']);
            };

            yield 'id:readonly' => function (SlovakiaVaccinations $slovakiaVaccinations) use ($_) {
                $publishedOn = DateTime::dateTimeFromString($_['published_on'], 'Y-m-d', true);

                return $slovakiaVaccinations
                    ->setId($publishedOn->format('Ymd'));
            };

            yield 'id' => function (RegionVaccinations $regionVaccinations, Region $region, ?SlovakiaVaccinations $slovakiaVaccinations) use ($_) {
                $update = null;

                // if we don't have daily aggregation skip (because is probably today or yesterday without nczi update)
                if (null === $slovakiaVaccinations) {
                    return null;
                }

                // take first available update in this order: power_bi, iza
                if (null !== $_['power_bi']) {
                    $update = $_['power_bi'];
                } elseif (null !== $_['iza']) {
                    $update = $_['iza'];
                }

                // nothing :(
                if (null === $update) {
                    return null;
                }

                $publishedOn = DateTime::dateTimeFromString($update['published_on'], 'Y-m-d', true);

                return $regionVaccinations
                    ->setId(Id::fromDateTimeAndInt($publishedOn, $region->getId()))
                    ->setRegion($region)
                    ->setPublishedOn($publishedOn)
                    ->setDose1Count((int)$update['dose1_count'])
                    ->setDose2Count((int)$update['dose2_count'])
                    ->setDose1Sum((int)$update['dose1_sum'])
                    ->setDose2Sum((int)$update['dose2_sum']);
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