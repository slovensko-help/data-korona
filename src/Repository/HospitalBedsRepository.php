<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hospital;
use App\Entity\TimeSeries\HospitalBeds as Entity;
use App\Repository\Traits\Paginable;
use App\Tool\DateTime;
use App\Tool\Id;
use Doctrine\Persistence\ManagerRegistry;

class HospitalBedsRepository extends AbstractRepository implements PaginableRepositoryInterface
{
    use Paginable;

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

            return $this->updateOrCreate(function (Entity $hospitalBeds) use ($item, $id, $publishedOn, $hospital) {
                return $hospitalBeds
                    ->setId($id)
                    ->setHospital($hospital)
                    ->setPublishedOn($publishedOn)
                    ->setReportedAt(DateTime::dateTimeFromString($item['DATUM_VYPL'], 'Y-m-d H:i:s'))
                    ->setCapacityAll($this->nullOrInt($item['ZAR_SPOLU']))
                    ->setCapacityCovid($this->nullOrInt($item['ZAR_MAX']))
                    ->setFreeAll($this->nullOrInt($item['ZAR_VOLNE']))
                    ->setOccupiedJisCovid($this->nullOrInt($item['COVID_JIS']))
                    ->setOccupiedOaimCovid($this->nullOrInt($item['COVID_OAIM']))
                    ->setOccupiedO2Covid($this->nullOrInt($item['COVID_O2']))
                    ->setOccupiedOtherCovid($this->nullOrInt($item['COVID_NONO2']));
            }, ['id' => $id]);
        }

        return null;
    }
}
