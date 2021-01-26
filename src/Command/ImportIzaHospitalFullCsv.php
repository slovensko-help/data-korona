<?php

namespace App\Command;

use App\Entity\Hospital;
use App\Entity\TimeSeries\HospitalBeds;
use App\Entity\TimeSeries\HospitalPatients;
use App\Entity\TimeSeries\HospitalStaff;
use App\Entity\Traits\HospitalBedsData;
use App\Entity\Traits\HospitalPatientsData;
use App\Repository\AbstractRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Query\QueryBuilder;
use Exception;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportIzaHospitalFullCsv extends AbstractImportTimeSeries
{
    const CSV_FILE = 'https://raw.githubusercontent.com/Institut-Zdravotnych-Analyz/covid19-data/main/OpenData_Slovakia_Covid_Hospital_Full.csv';

    protected static $defaultName = 'app:import:iza:hospital-full';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln($this->log('Downloading CSV file...'));

        $csv = file_get_contents(self::CSV_FILE);

        $output->writeln($this->log('DONE.'));

        $recordsCount = 0;
        $hospitals = [];

        $output->writeln($this->log('Updating hospitals/cities/districts/regions...'));

        /**
         * all record columns are referenced ONLY by uppercase keys
         */
        foreach ($this->csvRecords($csv) as $record) {
            $hospital = $this->hospital(
                $record,
                $this->city(
                    $record,
                    $this->district(
                        $record,
                        $this->region($record)
                    )
                )
            );

            if ($hospital instanceof Hospital) {
                $hospitals[$hospital->getCode()] = $hospital;
            }

            $recordsCount++;
        }

        $this->entityManager->flush();
        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating hospitalBeds, hospitalPatients and hospitalStaff. Records=' . $recordsCount . '...'));

        $count = 0;

        foreach ($this->csvRecords($csv) as $record) {
            $count++;

            $hospital = $hospitals[$record['KODPZS']] ?? null;

//            $this->hospitalBeds($record, $hospital);
//            $this->hospitalPatients($record, $hospital);
            $this->hospitalStaff($record, $hospital);

            if ($count % 1000 === 0) {
                $this->commitChangesToDb([HospitalBeds::class, HospitalPatients::class, HospitalStaff::class]);
            }
        }

        $this->commitChangesToDb([HospitalBeds::class, HospitalPatients::class, HospitalStaff::class]);

        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating aggregations...'));

        $this->updateAggregationsByDistric();
        $this->updateAggregationsByRegion();
        $this->updateSlovakiaAggregations();

        $output->writeln($this->log('DONE.'));

        return self::SUCCESS;
    }

    private function updateAggregationsByDistric()
    {
        $districts = $this->districtRepository->findAllIndexedById();

        $aggregationRecordTransformer = function(array $record) use ($districts) {
            $record = $this->aggregationRecordTransformer($record);

            $record['district'] = $districts[$record['district']];
            $record['id'] = $this->idFromDateTimeAndInt($record['published_on'], $record['district']->getId());

            return $record;
        };

        $aggregationQueryBuilder = function(string $sourceEntityTableName, string $sourceEntityDataTraitName, string $sourceEntityClassName) {
            return function(AbstractRepository $repository) use ($sourceEntityTableName, $sourceEntityDataTraitName, $sourceEntityClassName) {
                $queryBuilder = $this->aggregationQueryBuilder($repository, $sourceEntityTableName, $sourceEntityDataTraitName, $sourceEntityClassName);
                $queryBuilder = $this->groupByDistrictQueryBuilder($queryBuilder);

                return $queryBuilder;
            };
        };

        $this->districtHospitalBedsRepository->updateAllFromQuery(
            $aggregationQueryBuilder('hospital_beds', HospitalBedsData::class, HospitalBeds::class),
            $aggregationRecordTransformer);

        $this->districtHospitalPatientsRepository->updateAllFromQuery(
            $aggregationQueryBuilder('hospital_patients', HospitalPatientsData::class, HospitalPatients::class),
            $aggregationRecordTransformer);
    }

    private function updateAggregationsByRegion()
    {
        $regions = $this->regionRepository->findAllIndexedById();

        $aggregationRecordTransformer = function(array $record) use ($regions) {
            $record = $this->aggregationRecordTransformer($record);

            $record['region'] = $regions[$record['region']];
            $record['id'] = $this->idFromDateTimeAndInt($record['published_on'], $record['region']->getId());

            return $record;
        };

        $aggregationQueryBuilder = function(string $sourceEntityTableName, string $sourceEntityDataTraitName, string $sourceEntityClassName) {
            return function(AbstractRepository $repository) use ($sourceEntityTableName, $sourceEntityDataTraitName, $sourceEntityClassName) {
                $queryBuilder = $this->aggregationQueryBuilder($repository, $sourceEntityTableName, $sourceEntityDataTraitName, $sourceEntityClassName);
                $queryBuilder = $this->groupByRegionQueryBuilder($queryBuilder);

                return $queryBuilder;
            };
        };

        $this->regionHospitalBedsRepository->updateAllFromQuery(
            $aggregationQueryBuilder('hospital_beds', HospitalBedsData::class, HospitalBeds::class),
            $aggregationRecordTransformer);

        $this->regionHospitalPatientsRepository->updateAllFromQuery(
            $aggregationQueryBuilder('hospital_patients', HospitalPatientsData::class, HospitalPatients::class),
            $aggregationRecordTransformer);
    }

    private function updateSlovakiaAggregations()
    {
        $aggregationRecordTransformer = function(array $record){
            $record = $this->aggregationRecordTransformer($record);

            $record['id'] = $this->idFromDateTimeAndInt($record['published_on'], 0);

            return $record;
        };

        $aggregationQueryBuilder = function(string $sourceEntityTableName, string $sourceEntityDataTraitName, string $sourceEntityClassName) {
            return function(AbstractRepository $repository) use ($sourceEntityTableName, $sourceEntityDataTraitName, $sourceEntityClassName) {
                return $this->aggregationQueryBuilder($repository, $sourceEntityTableName, $sourceEntityDataTraitName, $sourceEntityClassName);
            };
        };

        $this->slovakiaHospitalBedsRepository->updateAllFromQuery(
            $aggregationQueryBuilder('hospital_beds', HospitalBedsData::class, HospitalBeds::class),
            $aggregationRecordTransformer);

        $this->slovakiaHospitalPatientsRepository->updateAllFromQuery(
            $aggregationQueryBuilder('hospital_patients', HospitalPatientsData::class, HospitalPatients::class),
            $aggregationRecordTransformer);
    }

    private function aggregationRecordTransformer(array $record) {
        $record['published_on'] = $this->dateTimeFromString($record['published_on'], 'Y-m-d', true);;
        $record['oldest_reported_at'] = $this->dateTimeFromString($record['oldest_reported_at'], 'Y-m-d H:i:s');
        $record['newest_reported_at'] = $this->dateTimeFromString($record['newest_reported_at'], 'Y-m-d H:i:s');

        return $record;
    }

    private function withHospitalAndCityQueryBuilder(QueryBuilder $queryBuilder)
    {
        return $queryBuilder
            ->innerJoin('data', 'hospital', 'h', 'data.hospital_id = h.id')
            ->innerJoin('h', 'city', 'c', 'h.city_id = c.id');
    }

    private function groupByDistrictQueryBuilder(QueryBuilder $queryBuilder)
    {
        return $this->withHospitalAndCityQueryBuilder($queryBuilder)
            ->addSelect('d.id AS district')
            ->innerJoin('c', 'district', 'd', 'c.district_id = d.id')
            ->addGroupBy('district');
    }

    private function groupByRegionQueryBuilder(QueryBuilder $queryBuilder)
    {
        return $this->withHospitalAndCityQueryBuilder($queryBuilder)
            ->innerJoin('c', 'district', 'd', 'c.district_id = d.id')
            ->innerJoin('d', 'region', 'r', 'd.region_id = r.id')
            ->addSelect('r.id AS region')
            ->addGroupBy('region');
    }

    private function aggregationQueryBuilder(AbstractRepository $repository, string $sourceEntityTableName, string $sourceEntityDataTraitName, string $sourceEntityClassName) {
        $aggregates = $repository->aggregatableColumns($sourceEntityDataTraitName, $sourceEntityClassName);

        $queryBuilder = $this->connection->createQueryBuilder()
            ->select(
                'data.published_on',
                'MIN(data.reported_at) AS oldest_reported_at',
                'MAX(data.reported_at) AS newest_reported_at'
            )
            ->from($sourceEntityTableName, 'data')
            ->groupBy('published_on');

        foreach ($aggregates as $aggregate) {
            $queryBuilder->addSelect("SUM($aggregate) AS $aggregate");
        };

        return $queryBuilder;
    }

    private function idFromDateTimeAndInt(DateTimeImmutable $date, int $id)
    {
        return $id + ((int)$date->format('ymd')) * 10000;
    }

    protected function log($message)
    {
        return '[' . date('Y-m-d H:i:s') . '] ' . $message;
    }

    private function hospitalBeds(array $record, ?Hospital $hospital)
    {
        if ($hospital instanceof Hospital) {
            $publishedOn = $this->dateTimeFromString($record['DAT_SPRAC'], 'Y-m-d H:i:s', true);
            $id = $this->idFromDateTimeAndInt($publishedOn, $hospital->getId());

            $this->updateOrCreate(function (?HospitalBeds $hospitalBeds) use ($record, $id, $publishedOn, $hospital) {
                if (null === $hospitalBeds) {
                    $hospitalBeds = new HospitalBeds();
                }

                return $hospitalBeds
                    ->setId($id)
                    ->setHospital($hospital)
                    ->setPublishedOn($publishedOn)
                    ->setReportedAt($this->dateTimeFromString($record['DATUM_VYPL'], 'Y-m-d H:i:s'))
                    ->setCapacityAll($this->nullOrInt($record['ZAR_SPOLU']))
                    ->setCapacityCovid($this->nullOrInt($record['ZAR_MAX']))
                    ->setFreeAll($this->nullOrInt($record['ZAR_VOLNE']))
                    ->setOccupiedJisCovid($this->nullOrInt($record['COVID_JIS']))
                    ->setOccupiedOaimCovid($this->nullOrInt($record['COVID_OAIM']))
                    ->setOccupiedO2Covid($this->nullOrInt($record['COVID_O2']))
                    ->setOccupiedOtherCovid($this->nullOrInt($record['COVID_NONO2']));
            }, $this->hospitalBedsRepository, ['id' => $id], false);
        }
    }

    protected function dateTimeFromString($datetimeString, $format, $resetTime = false): DateTimeImmutable
    {
        $date = DateTimeImmutable::createFromFormat($format, $datetimeString);

        if (false === $date) {
            throw new Exception('Datetime string "' . $datetimeString . '" is not in format "' . $format . '"');
        }

        if ($resetTime) {
            $date = $date->setTime(0, 0, 0);
        }

        return $date;
    }

    private function hospitalPatients(array $record, ?Hospital $hospital)
    {
        if ($hospital instanceof Hospital) {
            $publishedOn = $this->dateTimeFromString($record['DAT_SPRAC'], 'Y-m-d H:i:s', 'Y-m-d');
            $id = $this->idFromDateTimeAndInt($publishedOn, $hospital->getId());

            $this->updateOrCreate(function (?HospitalPatients $hospitalPatients) use ($record, $id, $publishedOn, $hospital) {
                if (null === $hospitalPatients) {
                    $hospitalPatients = new HospitalPatients();
                }

                return $hospitalPatients
                    ->setId($id)
                    ->setHospital($hospital)
                    ->setPublishedOn($publishedOn)
                    ->setReportedAt($this->dateTimeFromString($record['DATUM_VYPL'], 'Y-m-d H:i:s'))
                    ->setConfirmedCovid($this->nullOrInt($record['ZAR_COVID']))
                    ->setSuspectedCovid($this->nullOrInt($record['ZAR_COVID_HYPOT']))
                    ->setNonCovid($this->nullOrInt($record['ZAR_OBSADENE']))
                    ->setVentilatedCovid($this->nullOrInt($record['POSTELE_COVID_PL']));
            }, $this->hospitalPatientsRepository, ['id' => $id], false);
        }
    }

    private function hospitalStaff(array $record, ?Hospital $hospital)
    {
        if ($hospital instanceof Hospital) {
            $publishedOn = $this->dateTimeFromString($record['DAT_SPRAC'], 'Y-m-d H:i:s', 'Y-m-d');
            $id = $this->idFromDateTimeAndInt($publishedOn, $hospital->getId());

            $this->updateOrCreate(function (?HospitalStaff $hospitalStaff) use ($record, $id, $publishedOn, $hospital) {
                $hospitalStaff = $hospitalStaff ?? new HospitalStaff();

                return $hospitalStaff
                    ->setId($id)
                    ->setHospital($hospital)
                    ->setPublishedOn($publishedOn)
                    ->setReportedAt($this->dateTimeFromString($record['DATUM_VYPL'], 'Y-m-d H:i:s'))
                    ->setOutOfWorkRatioDoctor($this->nullOrFloat($record['PERSONAL_LEKAR_PERC_PN']))
                    ->setOutOfWorkRatioNurse($this->nullOrFloat($record['PERSONAL_SESTRA_PERC_PN']))
                    ->setOutOfWorkRatioOther($this->nullOrFloat($record['PERSONAL_OSTATNI_PERC_PN']))
                    ;
            }, $this->hospitalStaffRepository, ['id' => $id], false);
        }
    }

    private function commitChangesToDb(array $clearEntityClasses = [])
    {
        $this->entityManager->flush();

        foreach ($clearEntityClasses as $clearEntityClasse) {
            $this->entityManager->clear($clearEntityClasse);
        }
    }
}