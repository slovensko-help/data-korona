<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Hospital;
use App\Entity\TimeSeries\HospitalStaff;
use App\Entity\TimeSeries\HospitalStaff as Entity;
use App\Repository\Traits\Paginable;
use App\Tool\DateTime;
use App\Tool\Id;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Entity|null find($id, $lockMode = null, $lockVersion = null)
 * @method Entity|null findOneBy(array $criteria, array $orderBy = null)
 * @method Entity[]    findAll()
 * @method Entity[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class HospitalStaffRepository extends AbstractRepository implements PaginableRepositoryInterface
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

            $this->updateOrCreate(function (?HospitalStaff $hospitalStaff) use ($item, $id, $publishedOn, $hospital) {
                $hospitalStaff = $hospitalStaff ?? new HospitalStaff();

                return $hospitalStaff
                    ->setId($id)
                    ->setHospital($hospital)
                    ->setPublishedOn($publishedOn)
                    ->setReportedAt(DateTime::dateTimeFromString($item['DATUM_VYPL'], 'Y-m-d H:i:s'))
                    ->setOutOfWorkRatioDoctor($this->nullOrFloat($item['PERSONAL_LEKAR_PERC_PN']))
                    ->setOutOfWorkRatioNurse($this->nullOrFloat($item['PERSONAL_SESTRA_PERC_PN']))
                    ->setOutOfWorkRatioOther($this->nullOrFloat($item['PERSONAL_OSTATNI_PERC_PN']));
            }, ['id' => $id]);
        }

        return null;
    }
}
