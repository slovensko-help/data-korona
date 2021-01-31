<?php

namespace App\Command;

use App\Client\PowerBi\AbstractClient;
use App\Entity\City;
use App\Entity\District;
use App\Entity\Hospital;
use App\Entity\Region;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\RegionRepository;
use App\Repository\HospitalBedsRepository;
use App\Repository\HospitalPatientsRepository;
use App\Repository\HospitalRepository;
use App\Repository\HospitalStaffRepository;
use App\Repository\NcziMorningEmailRepository;
use App\Repository\NotificationRepository;
use App\Service\Content;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Generator;
use League\Csv\Reader;
use League\Csv\Statement;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;

abstract class AbstractImport extends Command
{
    protected $connection;
    protected $entityManager;
    protected $content;

    protected $regionRepository;
    protected $hospitalRepository;
    protected $districtRepository;
    protected $cityRepository;
    protected $ncziMorningEmailRepository;
    protected $notificationRepository;

    protected $parameterBag;
    protected $mailer;

    public function __construct(
        EntityManagerInterface $managerRegistry,
        Connection $connection,
        MailerInterface $mailer,
        Content $content,

        HospitalRepository $hospitalRepository,
        CityRepository $cityRepository,
        DistrictRepository $districtRepository,
        RegionRepository $regionRepository,

        NcziMorningEmailRepository $ncziMorningEmailRepository,

        NotificationRepository $notificationRepository,

        ParameterBagInterface $parameterBag,
        string $name = null
    )
    {
        parent::__construct($name);

        $this->connection = $connection;
        $this->entityManager = $managerRegistry;
        $this->content = $content;

        $this->hospitalRepository = $hospitalRepository;
        $this->cityRepository = $cityRepository;
        $this->districtRepository = $districtRepository;
        $this->regionRepository = $regionRepository;

        $this->ncziMorningEmailRepository = $ncziMorningEmailRepository;
        $this->notificationRepository = $notificationRepository;

        $this->parameterBag = $parameterBag;
        $this->mailer = $mailer;
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

    protected function commitChangesToDb()
    {
        $this->entityManager->flush();
        $this->entityManager->clear();
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
            $code = $this->uniqueHospitalCode($record['KODPZS'], $record['NAZOV']);
            return $this->findOrCreate(function () use ($code, $record, $city) {
                return (new Hospital())
                    ->setCity($city)
                    ->setTitle($record['NAZOV'])
                    ->setCode($code);
            }, $this->hospitalRepository, ['code' => $code]);
        }

        return null;
    }

    /**
     * @param callable $updateEntityCallback
     * @param EntityRepository $repository
     * @param array $criteria
     * @param false $flushAutomatically
     * @param false $returnBeforeAndAfterUpdate
     * @return mixed
     */
    protected function updateOrCreate(callable $updateEntityCallback, EntityRepository $repository, array $criteria, $flushAutomatically = false, $returnBeforeAndAfterUpdate = false)
    {
        $entity = $repository->findOneBy($criteria);
        $beforeEntity = null === $entity ? null : clone $entity;

        $entity = $updateEntityCallback($entity);

        if (null !== $entity) {
            $this->entityManager->persist($entity);

            if ($flushAutomatically) {
                $this->entityManager->flush();
            }
        }

        return $returnBeforeAndAfterUpdate ? [
            'before' => $beforeEntity,
            'after' => $entity,
        ] : $entity;
    }

    protected function nullOrInt($stringValue): ?int
    {
        return '' === $stringValue ? null : (int)$stringValue;
    }

    protected function nullOrFloat($stringValue): ?float
    {
        return '' === $stringValue ? null : round((float)str_replace(',', '.', $stringValue), 3);
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

    protected function log($message)
    {
        return '[' . date('Y-m-d H:i:s') . '] ' . $message;
    }

    protected function dumpPowerBiSchema(AbstractClient $repository, OutputInterface $output)
    {
        $schema = $repository->schema();

        (new Table($output))
            ->setHeaders(array_shift($schema))
            ->setRows($schema)
            ->render();
    }

    private function uniqueHospitalCode(string $code, string $name): string
    {
        if ('P99999999999' !== $code) {
            return $code;
        }

        return $code . '_' . substr(sha1($name), 0, 8);
    }

    private function isValidCode($code)
    {
        return !empty($code) && $code !== 'NA';
    }
}