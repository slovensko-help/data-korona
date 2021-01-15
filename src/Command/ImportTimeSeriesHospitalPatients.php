<?php

namespace App\Command;

use App\Entity\Aggregation\LatestHospitalPatients;
use App\Entity\District;
use App\Entity\Hospital;
use App\Entity\Region;
use App\Entity\TimeSeries\HospitalPatients;
use DateTimeImmutable;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportTimeSeriesHospitalPatients extends AbstractImportTimeSeries
{
    const CSV_FILE = 'https://raw.githubusercontent.com/Institut-Zdravotnych-Analyz/covid19-data/main/OpenData_Slovakia_Covid_Hospital.csv';

    protected static $defaultName = 'app:import:time-series:hospital-patients';

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /**
         * all record columns are referenced ONLY by lowercase keys
         */
        $count = 0;
        foreach ($this->csvRecords(file_get_contents(self::CSV_FILE)) as $record) {
            $count++;
            $this->hospitalPatients(
                $record,
                $this->hospital(
                    $record,
                    $this->district(
                        $record,
                        $this->region($record)
                    )
                )
            );

            if ($count % 1000 === 0) {
                $this->entityManager->flush(); # store hospitalPatiens updates to db
            }
        }

        $this->updateLatest();

        $this->entityManager->flush();  # store updates to db

        return self::SUCCESS;
    }

    private function updateLatest() {
        $latestHospitalPatientsRecords = $this->latestHospitalPatients();

        foreach ($this->hospitalRepository->findAll() as $hospital) {
            $this->updateOrCreate(function (?LatestHospitalPatients $latestHospitalPatients) use ($latestHospitalPatientsRecords, $hospital) {
                if (null === $latestHospitalPatients) {
                    $latestHospitalPatients = new LatestHospitalPatients();
                }

                $record = $latestHospitalPatientsRecords[$hospital->getId()];

                return $latestHospitalPatients
                    ->setLastDay($record['last_day'])
                    ->setHospital($hospital)
                    ->setJisCovid($this->nullOrInt($record['jis_covid']))
                    ->setAllConfirmedCovid($this->nullOrInt($record['all_confirmed_covid']))
                    ->setAllSuspectedAndConfirmedCovid($this->nullOrInt($record['all_suspected_and_confirmed_covid']))
                    ->setVentilationCovid($this->nullOrInt($record['ventilation_covid']));
            }, $this->latestHospitalPatientsRepository, ['hospital' => $hospital], false);
        }
    }

    private function latestHospitalPatients() {
        $records = $this->connection->executeQuery('
            SELECT
                t.last_day,
                t.hospital_id,
                hp.all_confirmed_covid,
                hp.all_suspected_and_confirmed_covid,
                hp.ventilation_covid,
                hp.jis_covid
            FROM
                hospital_patients AS hp
            INNER JOIN (
                SELECT
                hospital_id,
                MAX(`day`) AS last_day
            FROM
                hospital_patients
            GROUP BY
                hospital_id
            ) AS t
            ON
                hp.day = t.last_day AND hp.hospital_id = t.hospital_id');

        $result = [];

        foreach ($records as $record) {
            $result[(int) $record['hospital_id']] = $record;
        }

        return $result;
    }

//    private function hospital(array $record, District $district): Hospital
//    {
//        return $this->findOrCreate(function () use ($record, $district) {
//            return (new Hospital())
//                ->setDistrict($district)
//                ->setTitle($record['nemocnica'])
//                ->setCode($record['kodpzs']);
//        }, $this->hospitalRepository, ['code' => $record['kodpzs']]);
//    }

    private function hospitalPatients(array $record, Hospital $hospital)
    {
        $day = DateTimeImmutable::createFromFormat('Y-m-d', $record['datum'])->setTime(12, 0, 0)->format('Y-m-d');

        $this->updateOrCreate(function (?HospitalPatients $hospitalPatients) use ($record, $day, $hospital) {
            if (null === $hospitalPatients) {
                $hospitalPatients = new HospitalPatients();
            }

            return $hospitalPatients
                ->setDay($day)
                ->setHospital($hospital)
                ->setJisCovid($this->nullOrInt($record['potvrdeni_jis_oaim']))
                ->setAllConfirmedCovid($this->nullOrInt($record['obsadene_covid_lozka']))
                ->setAllSuspectedAndConfirmedCovid($this->nullOrInt($record['hospitalizovany']))
                ->setVentilationCovid($this->nullOrInt($record['plucna_vent_lozka']));
        }, $this->hospitalPatientsRepository, ['day' => $day, 'hospital' => $hospital], false);
    }
}