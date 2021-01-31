<?php

declare(strict_types=1);

namespace App\Repository\Aggregation;

use App\Repository\RegionRepository;
use App\Tool\Id;
use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;


abstract class AbstractRegionRepository extends AbstractRepository
{
    /**
     * @var RegionRepository
     */
    private $regionRepository;
    private $regionsIndexedById;

    public function __construct(ManagerRegistry $registry, string $entityClass)
    {
        parent::__construct($registry, $entityClass);
    }

    public function queryBuilder(): QueryBuilder
    {
        return $this->withHospitalAndCity(parent::queryBuilder())
            ->innerJoin('c', 'district', 'd', 'c.district_id = d.id')
            ->innerJoin('d', 'region', 'r', 'd.region_id = r.id')
            ->addSelect('r.id AS region')
            ->addGroupBy('region');
    }

    protected function hydrateItem(array $item): array
    {
        $item = parent::hydrateItem($item);
        $item['region'] = $this->regionsIndexedById[$item['region']];
        $item['id'] = Id::fromDateTimeAndInt($item['published_on'], $item['region']->getId());

        return $item;
    }

    protected function reloadDependantEntities()
    {
        $this->regionsIndexedById = $this->regionRepository->findAllIndexedById();
    }

    /**
     * @required
     * @param RegionRepository $regionRepository
     */
    public function setRegionRepository(RegionRepository $regionRepository)
    {
        $this->regionRepository = $regionRepository;
    }
}
