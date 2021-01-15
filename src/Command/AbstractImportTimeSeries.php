<?php

namespace App\Command;

use App\Entity\City;
use App\Entity\District;
use App\Entity\Hospital;
use App\Entity\Region;
use App\Entity\TimeSeries\HospitalPatients;
use App\Repository\CityRepository;
use App\Repository\DistrictHospitalBedsRepository;
use App\Repository\DistrictHospitalPatientsRepository;
use App\Repository\DistrictRepository;
use App\Repository\HospitalBedsRepository;
use App\Repository\HospitalPatientsRepository;
use App\Repository\HospitalRepository;
use App\Repository\RegionHospitalBedsRepository;
use App\Repository\RegionHospitalPatientsRepository;
use App\Repository\RegionRepository;
use App\Repository\SlovakiaHospitalBedsRepository;
use App\Repository\SlovakiaHospitalPatientsRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Generator;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\Console\Command\Command;

abstract class AbstractImportTimeSeries extends Command
{
    protected $connection;
    protected $entityManager;
    protected $regionRepository;
    protected $hospitalRepository;
    protected $districtRepository;
    protected $cityRepository;
    protected $hospitalPatientsRepository;
    protected $hospitalBedsRepository;
    protected $latestHospitalPatientsRepository;
    protected $districtHospitalBedsRepository;
    protected $regionHospitalBedsRepository;
    protected $slovakiaHospitalBedsRepository;
    protected $districtHospitalPatientsRepository;
    protected $regionHospitalPatientsRepository;
    protected $slovakiaHospitalPatientsRepository;

    public function __construct(
        EntityManagerInterface $managerRegistry,
        Connection $connection,
        HospitalRepository $hospitalRepository,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        RegionRepository $regionRepository,
        HospitalBedsRepository $hospitalBedsRepository,
        HospitalPatientsRepository $hospitalPatientsRepository,
        DistrictHospitalBedsRepository $districtHospitalBedsRepository,
        RegionHospitalBedsRepository $regionHospitalBedsRepository,
        SlovakiaHospitalBedsRepository $slovakiaHospitalBedsRepository,
        DistrictHospitalPatientsRepository $districtHospitalPatientsRepository,
        RegionHospitalPatientsRepository $regionHospitalPatientsRepository,
        SlovakiaHospitalPatientsRepository $slovakiaHospitalPatientsRepository,
        string $name = null
    )
    {
        parent::__construct($name);

        $this->connection = $connection;
        $this->entityManager = $managerRegistry;

        $this->hospitalRepository = $hospitalRepository;
        $this->cityRepository = $cityRepository;
        $this->districtRepository = $districtRepository;
        $this->regionRepository = $regionRepository;

        $this->hospitalBedsRepository = $hospitalBedsRepository;
        $this->hospitalPatientsRepository = $hospitalPatientsRepository;
        $this->districtHospitalBedsRepository = $districtHospitalBedsRepository;
        $this->regionHospitalBedsRepository = $regionHospitalBedsRepository;
        $this->slovakiaHospitalBedsRepository = $slovakiaHospitalBedsRepository;

        $this->districtHospitalPatientsRepository = $districtHospitalPatientsRepository;
        $this->regionHospitalPatientsRepository = $regionHospitalPatientsRepository;
        $this->slovakiaHospitalPatientsRepository = $slovakiaHospitalPatientsRepository;
    }

    private function isValidCode($code) {
        return !empty($code) && $code !== 'NA';
    }

    protected function region(array $record): ?Region
    {
        if ($this->isValidCode($record['SIDOU_KRAJ_KOD_ST'])) {
            return $this->findOrCreate(function () use ($record) {
                return (new Region())
                    ->setCode($record['SIDOU_KRAJ_KOD_ST'])
                    ->setTitle(str_replace(' kraj', '', $record['SIDOU_KRAJ_POP_ST']));
            }, $this->regionRepository, ['code' => $record['SIDOU_KRAJ_KOD_ST']]);
        }

        return null;
    }

    protected function findOrCreate(callable $newEntityCallback, EntityRepository $repository, array $criteria, bool $cacheEnabled = true, ?array $keyParts = null)
    {
        if ($cacheEnabled) {
            $entity = $this->cachedEntity($repository, $criteria, $keyParts);

            if (null !== $entity) {
                return $entity;
            }
        }

        $entity = $newEntityCallback();

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    protected function cachedEntity(EntityRepository $repository, array $criteria, ?array $keyParts = null)
    {
        static $cachedEntities;

        $class = $repository->getClassName();
        $key = join('-', null === $keyParts ? $criteria : $keyParts);

        $cachedEntities = $cachedEntities ?? [];
        $cachedEntities[$class] = $cachedEntities[$class] ?? [];

        if (isset($cachedEntities[$class][$key])) {
            return $cachedEntities[$class][$key];
        }

        $entity = $repository->findOneBy($criteria);

        if (null === $entity) {
            return null;
        }

        $cachedEntities[$class][$key] = $entity;

        return $entity;
    }

    protected function district(array $record, ?Region $region): ?District
    {
        if ($this->isValidCode($record['SIDOU_OKRES_KOD_ST']) && $region instanceof Region) {
            return $this->findOrCreate(function () use ($record, $region) {
                return (new District())
                    ->setRegion($region)
                    ->setCode($record['SIDOU_OKRES_KOD_ST'])
                    ->setTitle(str_replace('Okres ', '', $record['SIDOU_OKRES_POP_ST']));
            }, $this->districtRepository, ['code' => $record['SIDOU_OKRES_KOD_ST']]);
        }

        return null;
    }

    protected function city(array $record, ?District $district): ?City
    {
        if ($this->isValidCode($record['SIDOU_OBEC_KOD_ST']) && $district instanceof District) {

            if (strlen($record['SIDOU_OBEC_KOD_ST']) === 6) {
                $record['SIDOU_OBEC_KOD_ST'] = $district->getCode() . $record['SIDOU_OBEC_KOD_ST'];
            }

            return $this->findOrCreate(function () use ($record, $district) {
                return (new City())
                    ->setDistrict($district)
                    ->setCode($record['SIDOU_OBEC_KOD_ST'])
                    ->setTitle($record['SIDOU_OBEC_POP_ST']);
            }, $this->cityRepository, ['code' => $record['SIDOU_OBEC_KOD_ST']]);
        }

        return null;
    }

    protected function hospital(array $record, ?City $city): ?Hospital
    {
        if ($this->isValidCode($record['KODPZS']) && $city instanceof City) {
            return $this->findOrCreate(function () use ($record, $city) {
                return (new Hospital())
                    ->setCity($city)
                    ->setTitle($record['NAZOV'])
                    ->setCode($record['KODPZS']);
            }, $this->hospitalRepository, ['code' => $record['KODPZS']]);
        }

        return null;
    }

    protected function updateOrCreate(callable $updateEntityCallback, EntityRepository $repository, array $criteria, $flushAutomatically = false)
    {
        $entity = $updateEntityCallback($repository->findOneBy($criteria));

        if (null !== $entity) {
            $this->entityManager->persist($entity);

            if ($flushAutomatically) {
                $this->entityManager->flush();
            }
        }

        return $entity;
    }

    protected function nullOrInt($stringValue): ?int
    {
        return '' === $stringValue ? null : (int)$stringValue;
    }

    protected function csvRecords($csvString): Generator
    {
        $csv = Reader::createFromString($csvString);
        $csv->setDelimiter(';');

        $records = Statement::create()->process($csv, array_map('strtoupper', $csv->fetchOne()));

        foreach ($records as $i => $record) {
            if ($i > 0) {
                yield $record;
            }
        }
    }
}