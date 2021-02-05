<?php

namespace App\Client\Iza;

use App\Entity\City;
use App\Entity\District;
use App\Entity\Hospital;
use App\Entity\Region;
use App\Entity\TimeSeries\HospitalBeds;
use App\Tool\DateTime;
use App\Tool\Id;

class HospitalsClient extends AbstractClient
{
    const CSV_FILE = 'Hospitals/OpenData_Slovakia_Covid_Hospital_Full.csv';

    protected function dataItemToEntities(array $dataItem): array
    {
        return [
            [Region::class, 'code', $this->region($dataItem)],
            [District::class, 'code', $this->district($dataItem)],
            [City::class, 'code', $this->city($dataItem)],
            [Hospital::class, 'code', $this->hospital($dataItem)],
            [HospitalBeds::class, 'id', $this->hospitalBeds($dataItem)],
        ];
    }

    private function region(array $_): ?callable
    {
        if ($this->isInvalidCode($_['SIDOU_KRAJ_KOD_ST'])) {
            return null;
        }
        return function(Region $region) use ($_) {
            return $region
                ->setCode($_['SIDOU_KRAJ_KOD_ST'])
                ->setTitle($_['SIDOU_KRAJ_POP_ST']);
        };
    }

    private function district(array $_): ?callable
    {
        if ($this->isInvalidCode($_['SIDOU_OKRES_KOD_ST'])) {
            return null;
        }

        return function(District $district, ?Region $region) use ($_) {
            return $district
                ->setRegion($region)
                ->setCode($_['SIDOU_OKRES_KOD_ST'])
                ->setTitle($_['SIDOU_OKRES_POP_ST']);
        };
    }

    private function city(array $_): ?callable
    {
        if ($this->isInvalidCode($_['SIDOU_OBEC_KOD_ST'])) {
            return null;
        }

        return function(City $city, District $district) use ($_) {
            return $city
                ->setDistrict($district)
                ->setCode($this->fixedCityCode($_['SIDOU_OBEC_KOD_ST'], $district))
                ->setTitle($_['SIDOU_OBEC_POP_ST']);
        };
    }

    private function hospital(array $_): callable
    {
        return function(Hospital $hospital, City $city) use ($_) {
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
}