<?php

namespace App\Command;

use App\Entity\City;
use App\Entity\District;
use App\Entity\Hospital;
use App\Entity\Raw\HospitalVaccinationSubstitute;
use App\Entity\Region;
use App\Entity\VaccinationContacts;
use Generator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class VaccinationSubstitutesImport extends AbstractImport
{
    const CSV_FILE = '@project_dir/data/vaccination-hospitals.csv';
    const JSON_FILE = '@project_dir/data/vaccination-substitutes.json';

    protected static $defaultName = 'app:import:vaccination-substitutes';

    /** @var SluggerInterface */
    private $slugger;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
//        $output->writeln($this->log('Reading hospitals CSV file...'));
//        $csvContent = $this->content->load(self::CSV_FILE);
//        $output->writeln($this->log('DONE.'));
//
//        $output->writeln($this->log('Reading substitutes JSON file...'));
//        $jsonContent = $this->content->load(self::JSON_FILE);
//        $output->writeln($this->log('DONE.'));
//
//        $output->writeln($this->log('Updating vaccination hospitals ...'));
//        $this->persist($this->csvRecords($csvContent),
//            function (array $_) {
//                yield 'code' => $this->region($_);
//                yield 'code' => $this->district($_);
//                yield 'code' => $this->city($_);
//                yield 'code' => $this->hospital($_);
//            }
//        );
//        $output->writeln($this->log('DONE.'));
//
//        $output->writeln($this->log('Updating raw vaccination substitutes...'));
//        $this->persist($this->items($jsonContent),
//            function (array $_) {
//                yield 'id' => function (HospitalVaccinationSubstitute $vaccinationSubstituteHospital) use ($_) {
//                    return $vaccinationSubstituteHospital
//                        ->setId($this->vaccinationSubstituteHospitalId($_['name']))
//                        ->setRegionName($_['region'])
//                        ->setCityName($_['city'])
//                        ->setHospitalName($_['name'])
//                        ->setLink($_['link'])
//                        ->setEmail($_['email'])
//                        ->setPhones(empty($_['phone']) ? null : implode("\n", $_['phone']))
//                        ->setNote($_['note']);
//                };
//            }
//        );
//        $output->writeln($this->log('DONE.'));

        $output->writeln($this->log('Updating vaccination substitutes...'));
        $this->persist($this->entityManager->getRepository(HospitalVaccinationSubstitute::class)->findAll(),
            function (HospitalVaccinationSubstitute $_) {
                yield 'id' => function (VaccinationContacts $vaccinationContact) use ($_) {
                    if (null === $_->getHospital()) {
                        return null;
                    }
                    return $vaccinationContact
                        ->setId($_->getHospital()->getId())
                        ->setHospital($_->getHospital())
                        ->setSubstitutesEmails($_->getEmail())
                        ->setSubstitutesPhones($_->getPhones())
                        ->setSubstitutesLink($_->getLink())
                        ->setSubstitutesNote($_->getNote());
                };
            }
        );
        $output->writeln($this->log('DONE.'));

        return self::SUCCESS;
    }

    protected function fixedCityCode(string $code, District $district): string
    {
        if (strlen($code) === 6) {
            return $district->getCode() . $code;
        }

        return $code;
    }

    protected function isInvalidCode($code)
    {
        return empty($code) || $code === 'NA';
    }

    protected function fixedHospitalCode(string $code, string $name): string
    {
        return substr(sha1($name), 0, 16);
    }

    private function region(array $_): callable
    {
        return function (Region $region) use ($_) {
            if ($this->isInvalidCode($_['KRAJ_KOD'])) {
                return null;
            }
            return $region
                ->setCode($_['KRAJ_KOD'])
                ->setTitle(str_replace(' kraj', '', $_['KRAJ']));
        };
    }

    private function district(array $_): callable
    {
        return function (District $district, ?Region $region) use ($_) {
            if (null === $region || $this->isInvalidCode($_['OKRES_KOD'])) {
                return null;
            }
            return $district
                ->setRegion($region)
                ->setCode($_['OKRES_KOD'])
                ->setTitle(str_replace('Okres ', '', $_['OKRES']));
        };
    }

    private function city(array $_): callable
    {
        return function (City $city, ?District $district) use ($_) {
            if (null === $district || $this->isInvalidCode($_['OBEC_KOD'])) {
                return null;
            }
            return $city
                ->setDistrict($district)
                ->setCode($this->fixedCityCode($_['OBEC_KOD'], $district))
                ->setTitle($_['OBEC']);
        };
    }

    private function hospital(array $_): callable
    {
        return function (Hospital $hospital, ?City $city) use ($_) {
            if (null === $city) {
                return null;
            }

            return $hospital
                ->setCity($city)
                ->setCode($this->fixedHospitalCode($_['IDENTIFZAR'], $_['NAZZAR']))
                ->setTitle($_['NAZZAR']);
        };
    }

    private function vaccinationSubstituteHospitalId(string $name): string
    {
        return $this->slugger
            ->slug($name, '')
            ->lower()
            ->replaceMatches('/[aeiouy]/', '')
            ->slice(0, 100);
    }

    private function items(string $jsonContent): Generator
    {
        foreach (json_decode($jsonContent, true) as $region) {
            foreach ($region['places'] as $place) {
                $place['region'] = $region['name'];
                yield $place;
            }
        }
    }

    /**
     * @required
     * @param SluggerInterface $slugger
     */
    public function setSlugger(SluggerInterface $slugger): void
    {
        $this->slugger = $slugger;
    }
}