<?php

declare(strict_types=1);

namespace App\Repository\Aggregation;

use App\Repository\DistrictRepository;
use App\Tool\Id;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

abstract class AbstractDistrictRepository extends AbstractRepository
{
    /**
     * @var DistrictRepository
     */
    private $districtRepository;
    private $districtsIndexedById;

    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    public function queryBuilder(): QueryBuilder
    {
        return $this->withHospitalAndCity(parent::queryBuilder())
            ->addSelect('d.id AS district')
            ->innerJoin('c', 'district', 'd', 'c.district_id = d.id')
            ->addGroupBy('district');
    }

    protected function hydrateItem(array $item): array
    {
        $item = parent::hydrateItem($item);
        $item['district'] = $this->districtsIndexedById[$item['district']];
        $item['id'] = Id::fromDateTimeAndInt($item['published_on'], $item['district']->getId());

        return $item;
    }

    protected function reloadDependantEntities()
    {
        $this->districtsIndexedById = $this->districtRepository->findAllIndexedById();
    }

    /**
     * @required
     * @param DistrictRepository $districtRepository
     */
    public function setDistrictRepository(DistrictRepository $districtRepository)
    {
        $this->districtRepository = $districtRepository;
    }
}
