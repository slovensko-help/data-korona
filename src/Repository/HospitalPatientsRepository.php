<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hospital;
use App\Entity\TimeSeries\HospitalPatients as Entity;
use App\Tool\DateTime;
use App\Tool\Id;
use Doctrine\Persistence\ManagerRegistry;

class HospitalPatientsRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Entity::class);
    }

    public function save(array $item, ...$relatedEntities): ?Entity
    {
        $hospital = $relatedEntities[0];

        if ($hospital instanceof Hospital) {
            $publishedOn = DateTime::dateTimeFromString($item['DAT_SPRAC'], 'Y-m-d H:i:s', true);
            $id = Id::fromDateTimeAndInt($publishedOn, $hospital->getId());

            $this->updateOrCreate(function (Entity $hospitalPatients) use ($item, $id, $publishedOn, $hospital) {
                return $hospitalPatients
                    ->setId($id)
                    ->setHospital($hospital)
                    ->setPublishedOn($publishedOn)
                    ->setReportedAt(DateTime::dateTimeFromString($item['DATUM_VYPL'], 'Y-m-d H:i:s'))
                    ->setConfirmedCovid($this->nullOrInt($item['ZAR_COVID']))
                    ->setSuspectedCovid($this->nullOrInt($item['ZAR_COVID_HYPOT']))
                    ->setNonCovid($this->nullOrInt($item['ZAR_OBSADENE']))
                    ->setVentilatedCovid($this->nullOrInt($item['POSTELE_COVID_PL']));
            }, ['id' => $id]);
        }

        return null;
    }
}
