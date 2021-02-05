<?php

namespace App\Client\Iza;

use App\Entity\City;
use App\Entity\District;
use App\Entity\Hospital;
use App\Entity\Region;
use App\Entity\TimeSeries\HospitalBeds;
use App\Entity\TimeSeries\HospitalPatients;
use App\Entity\TimeSeries\HospitalPatients as Entity;
use App\Entity\TimeSeries\HospitalStaff;
use App\Tool\DateTime;
use App\Tool\Id;

class HospitalsClient extends AbstractClient
{
    const CSV_FILE = 'Hospitals/OpenData_Slovakia_Covid_Hospital_Full.csv';

    public function entities(): callable
    {
        return function ($_) {
            yield 'code' => $this->region($_);
            yield 'code' => $this->district($_);
            yield 'code' => $this->city($_);
            yield 'code' => $this->hospital($_);
            yield 'id' => $this->hospitalBeds($_);
            yield 'id' => $this->hospitalPatients($_);
            yield 'id' => $this->hospitalStaff($_);
        };
    }

    private function region(array $_): ?callable
    {
        if (!empty($_) && $this->isInvalidCode($_['SIDOU_KRAJ_KOD_ST'])) {
            return null;
        }
        return function (Region $region) use ($_) {
            return $region
                ->setCode($_['SIDOU_KRAJ_KOD_ST'])
                ->setTitle($_['SIDOU_KRAJ_POP_ST']);
        };
    }

    private function district(array $_): ?callable
    {
        if (!empty($_) && $this->isInvalidCode($_['SIDOU_OKRES_KOD_ST'])) {
            return null;
        }

        return function (District $district, ?Region $region) use ($_) {
            return $district
                ->setRegion($region)
                ->setCode($_['SIDOU_OKRES_KOD_ST'])
                ->setTitle($_['SIDOU_OKRES_POP_ST']);
        };
    }

    private function city(array $_): ?callable
    {
        if (!empty($_) && $this->isInvalidCode($_['SIDOU_OBEC_KOD_ST'])) {
            return null;
        }

        return function (City $city, District $district) use ($_) {
            return $city
                ->setDistrict($district)
                ->setCode($this->fixedCityCode($_['SIDOU_OBEC_KOD_ST'], $district))
                ->setTitle($_['SIDOU_OBEC_POP_ST']);
        };
    }

    private function hospital(array $_): callable
    {
        return function (Hospital $hospital, City $city) use ($_) {
            return $hospital
                ->setCity($city)
                ->setCode($this->fixedHospitalCode($_['KODPZS'], $_['NAZOV']))
                ->setTitle($_['NAZOV']);
        };
    }

    private function hospitalBeds(array $_): callable
    {
        return function (HospitalBeds $hospitalBeds, Hospital $hospital) use ($_) {
            $publishedOn = DateTime::dateTimeFromString($_['DAT_SPRAC'], 'Y-m-d H:i:s', true);
            return $hospitalBeds
                ->setId(Id::fromDateTimeAndInt($publishedOn, $hospital->getId()))
                ->setHospital($hospital)
                ->setPublishedOn($publishedOn)
                ->setReportedAt(DateTime::dateTimeFromString($_['DATUM_VYPL'], 'Y-m-d H:i:s'))
                ->setCapacityAll($this->nullOrInt($_['ZAR_SPOLU']))
                ->setCapacityCovid($this->nullOrInt($_['ZAR_MAX']))
                ->setFreeAll($this->nullOrInt($_['ZAR_VOLNE']))
                ->setOccupiedJisCovid($this->nullOrInt($_['COVID_JIS']))
                ->setOccupiedOaimCovid($this->nullOrInt($_['COVID_OAIM']))
                ->setOccupiedO2Covid($this->nullOrInt($_['COVID_O2']))
                ->setOccupiedOtherCovid($this->nullOrInt($_['COVID_NONO2']));
        };
    }

    private function hospitalPatients(array $_): callable
    {
        return function (HospitalPatients $hospitalPatients, Hospital $hospital) use ($_) {
            $publishedOn = DateTime::dateTimeFromString($_['DAT_SPRAC'], 'Y-m-d H:i:s', true);
            return $hospitalPatients
                ->setId(Id::fromDateTimeAndInt($publishedOn, $hospital->getId()))
                ->setHospital($hospital)
                ->setPublishedOn($publishedOn)
                ->setReportedAt(DateTime::dateTimeFromString($_['DATUM_VYPL'], 'Y-m-d H:i:s'))
                ->setConfirmedCovid($this->nullOrInt($_['ZAR_COVID']))
                ->setSuspectedCovid($this->nullOrInt($_['ZAR_COVID_HYPOT']))
                ->setNonCovid($this->nullOrInt($_['ZAR_OBSADENE']))
                ->setVentilatedCovid($this->nullOrInt($_['POSTELE_COVID_PL']));
        };
    }

    private function hospitalStaff(array $_): callable
    {
        return function (HospitalStaff $hospitalStaff, Hospital $hospital) use ($_) {
            $publishedOn = DateTime::dateTimeFromString($_['DAT_SPRAC'], 'Y-m-d H:i:s', true);
            return $hospitalStaff
                ->setId(Id::fromDateTimeAndInt($publishedOn, $hospital->getId()))
                ->setHospital($hospital)
                ->setPublishedOn($publishedOn)
                ->setReportedAt(DateTime::dateTimeFromString($_['DATUM_VYPL'], 'Y-m-d H:i:s'))
                ->setOutOfWorkRatioDoctor($this->nullOrFloat($_['PERSONAL_LEKAR_PERC_PN']))
                ->setOutOfWorkRatioNurse($this->nullOrFloat($_['PERSONAL_SESTRA_PERC_PN']))
                ->setOutOfWorkRatioOther($this->nullOrFloat($_['PERSONAL_OSTATNI_PERC_PN']));
        };
    }
}