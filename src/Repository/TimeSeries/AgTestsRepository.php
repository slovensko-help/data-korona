<?php

namespace App\Repository\TimeSeries;


use App\Entity\Aggregation\DistrictAgTests;
use App\Entity\Aggregation\RegionAgTests;
use App\Entity\Aggregation\SlovakiaAgTests;
use App\Entity\District;
use App\Entity\Region;
use App\Repository\ServiceEntityRepository;
use App\Service\AgTest;
use App\Tool\DateTime;
use App\Tool\Id;
use DateTimeImmutable;
use Doctrine\Persistence\ManagerRegistry;
use Exception;

class AgTestsRepository extends ServiceEntityRepository
{
    /** @var AgTest */
    private $agTest;

    /** @var DateTimeImmutable */
    private $yesterday;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SlovakiaAgTests::class);

        $this->yesterday = (new DateTimeImmutable('yesterday'))->setTime(0, 0);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function slovakiaAgTests(): array
    {
        return $this->agTest->dailyStats();
    }

    public function regionAgTests(): array
    {
        return $this->agTest->regionalDailyStats(new DateTimeImmutable('2020-01-01'));
    }

    public function districtAgTests(): array
    {
        return $this->agTest->districtualDailyStats(new DateTimeImmutable('2020-01-01'));
    }

    public function slovakiaAgTestsEntities(): callable
    {
        return function (array $_) {
            yield 'id' => function (SlovakiaAgTests $slovakiaAgTests) use ($_) {
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

                $positivesCount = (int) $update['positives_count'];
                $negativesCount = (int) $update['negatives_count'];

                return $slovakiaAgTests
                    ->setId($publishedOn->format('Ymd'))
                    ->setPublishedOn($publishedOn)
                    ->setPositivesCount($positivesCount)
                    ->setNegativesCount($negativesCount)
                    ->setPositivityRate(0 === $positivesCount + $negativesCount ? null : round($positivesCount / ($positivesCount + $negativesCount) * 100000, 3))
                    ->setPositivesSum((int) $update['positives_sum'])
                    ->setNegativesSum((int) $update['negatives_sum']);
            };
        };
    }

    public function regionAgTestsEntities(): callable
    {
        return function (array $_) {
            yield 'id:readonly' => function (Region $region) use ($_) {
                return $region
                    ->setId((int)$_['region_id']);
            };

            yield 'id:readonly' => function (SlovakiaAgTests $slovakiaAgTests) use ($_) {
                $publishedOn = DateTime::dateTimeFromString($_['published_on'], 'Y-m-d', true);

                return $slovakiaAgTests
                    ->setId($publishedOn->format('Ymd'));
            };

            yield 'id' => function (RegionAgTests $regionAgTests, ?Region $region, ?SlovakiaAgTests $slovakiaAgTests) use ($_) {
                $update = null;

                // if we don't have daily aggregation skip (because is probably today or yesterday without nczi update)
                if (null === $slovakiaAgTests) {
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
                $positivesCount = (int) $update['positives_count'];
                $negativesCount = (int) $update['negatives_count'];

                return $regionAgTests
                    ->setId(Id::fromDateTimeAndInt($publishedOn, null === $region ? 0 : $region->getId()))
                    ->setRegion($region)
                    ->setPublishedOn($publishedOn)
                    ->setPositivesCount($positivesCount)
                    ->setNegativesCount($negativesCount)
                    ->setPositivityRate(0 === $positivesCount + $negativesCount ? null : round($positivesCount / ($positivesCount + $negativesCount) * 100000, 3))
                    ->setPositivesSum((int) $update['positives_sum'])
                    ->setNegativesSum((int) $update['negatives_sum']);
            };
        };
    }

    public function districtAgTestsEntities(): callable
    {
        return function (array $_) {
            yield 'id:readonly' => function (District $district) use ($_) {
                return $district
                    ->setId((int)$_['district_id']);
            };

            yield 'id:readonly' => function (SlovakiaAgTests $slovakiaAgTests) use ($_) {
                $publishedOn = DateTime::dateTimeFromString($_['published_on'], 'Y-m-d', true);

                return $slovakiaAgTests
                    ->setId($publishedOn->format('Ymd'));
            };

            yield 'id' => function (DistrictAgTests $regionAgTests, ?District $district, ?SlovakiaAgTests $slovakiaAgTests) use ($_) {
                $update = null;

                // if we don't have daily aggregation skip (because is probably today or yesterday without nczi update)
                if (null === $slovakiaAgTests) {
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
                $positivesCount = (int) $update['positives_count'];
                $negativesCount = (int) $update['negatives_count'];

                return $regionAgTests
                    ->setId(Id::fromDateTimeAndInt($publishedOn, null === $district ? 0 : $district->getId()))
                    ->setDistrict($district)
                    ->setPublishedOn($publishedOn)
                    ->setPositivesCount($positivesCount)
                    ->setNegativesCount($negativesCount)
                    ->setPositivityRate(0 === $positivesCount + $negativesCount ? null : round($positivesCount / ($positivesCount + $negativesCount) * 100000, 3))
                    ->setPositivesSum((int) $update['positives_sum'])
                    ->setNegativesSum((int) $update['negatives_sum']);
            };
        };
    }

    /**
     * @required
     * @param AgTest $agTest
     */
    public function setAgTest(AgTest $agTest): void
    {
        $this->agTest = $agTest;
    }
}
