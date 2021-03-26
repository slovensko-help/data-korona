<?php

namespace App\Controller\Front;

use App\Controller\AbstractController;
use App\Service\AgTest;
use App\Service\Vaccination;
use DateTimeImmutable;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class StatsController extends AbstractController
{
    const FIVE_MINUTES = 1;

    /**
     * @Route("/stats/vaccinations/{section}", methods={"GET"})
     * @param Vaccination $vaccination
     * @param CacheInterface $cache
     * @param string $section
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function vaccinations(Vaccination $vaccination, CacheInterface $cache, string $section = '')
    {

        return $cache->get('stats-vaccinations-' . $section . rand(), function (ItemInterface $item) use ($section, $vaccination) {
            $item->expiresAfter($cacheTtl ?? static::FIVE_MINUTES);

            $data = [
                'section' => $section,
                'template' => $section,
                'tabs' => array_map(function ($tab) use ($section) {
                    return $tab + [
                            'url' => $this->generateUrl('app_front_stats_vaccinations', ['section' => $tab['section']]),
                            'is_active' => $tab['section'] === $section,
                        ];
                }, [
                    ['title' => 'Prehľad', 'section' => '',],
                    ['title' => 'Po dňoch', 'section' => 'by-day',],
                    ['title' => 'Po dňoch a krajoch', 'section' => 'by-day-and-region',],
                    ['title' => 'Po dňoch a vakcínach', 'section' => 'by-day-and-vaccine',],
                    ['title' => 'Po dňoch, krajoch a vakcínach', 'section' => 'by-day-and-region-and-vaccine',],
                    ['title' => 'Po krajoch', 'section' => 'by-region',],
                    ['title' => 'Po vakcínach', 'section' => 'by-vaccine',],
                    ['title' => 'Po vakcínach a krajoch', 'section' => 'by-vaccine-and-region',],
                ])
            ];

            switch ($section) {
                case 'by-day':
                    $data = ['stats' => $vaccination->dailyStats(),] + $data;
                    break;
                case 'by-day-and-region':
                    $data = ['stats' => $vaccination->regionalDailyStats(),] + $data;
                    break;
                case 'by-day-and-region-and-vaccine':
                    $data = [
                            'template' => 'table',
                            'stats' => $vaccination->statsByDayAndRegionAndVaccine(),
                            'header' => [
                                'published_on' => 'Deň',
                                'region_title' => 'Kraj',
                                'vaccine_title' => 'Vakcína',
                                'dose1_count' => 'Δ 1. dávka',
                                'dose2_count' => 'Δ 2. dávka',
                                'dose1_sum' => 'Σ 1. dávka',
                                'dose2_sum' => 'Σ 2. dávka',
                            ],
                        ] + $data;
                    break;
                case 'by-day-and-vaccine':
                    $data = [
                            'template' => 'table',
                            'stats' => $vaccination->statsByDayAndVaccine(),
                            'header' => [
                                'published_on' => 'Deň',
                                'vaccine_title' => 'Vakcína',
                                'dose1_count' => 'Δ 1. dávka',
                                'dose2_count' => 'Δ 2. dávka',
                                'dose1_sum' => 'Σ 1. dávka',
                                'dose2_sum' => 'Σ 2. dávka',
                            ],
                        ] + $data;
                    break;
                case 'by-region':
                    $data = [
                            'template' => 'table',
                            'stats' => $vaccination->statsByRegion(),
                            'header' => [
                                'region_title' => 'Kraj',
                                'dose1_sum' => 'Σ 1. dávka',
                                'dose2_sum' => 'Σ 2. dávka',
                            ],
                        ] + $data;
                    break;
                case 'by-vaccine':
                    $data = [
                            'template' => 'table',
                            'stats' => $vaccination->statsByVaccine(),
                            'header' => [
                                'vaccine_title' => 'Vakcína',
                                'manufacturer' => 'Dodávateľ',
                                'dose1_sum' => 'Σ 1. dávka',
                                'dose2_sum' => 'Σ 2. dávka',
                            ],
                        ] + $data;
                    break;
                case 'by-vaccine-and-region':
                    $data = [
                            'template' => 'table',
                            'stats' => $vaccination->statsByVaccineAndRegion(),
                            'header' => [
                                'vaccine_title' => 'Vakcína',
                                'region_title' => 'Kraj',
                                'dose1_sum' => 'Σ 1. dávka',
                                'dose2_sum' => 'Σ 2. dávka',
                            ],
                        ] + $data;
                    break;
                default:
                    $data = $data + ['stats' => $vaccination->updateStats()];
            }

            return $this->render('Vaccinations/index.html.twig', $data);
        });
    }

    /**
     * @Route("/stats/ag-tests/{section}", methods={"GET"})
     * @param AgTest $agTest
     * @param CacheInterface $cache
     * @param string $section
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function agTests(AgTest $agTest, CacheInterface $cache, string $section = '')
    {
        return $cache->get('stats-ag-tests-' . $section . rand(), function (ItemInterface $item) use ($section, $agTest) {
            $item->expiresAfter($cacheTtl ?? static::FIVE_MINUTES);

            $data = [
                'section' => $section,
                'template' => $section,
                'tabs' => array_map(function ($tab) use ($section) {
                    return $tab + [
                            'url' => $this->generateUrl('app_front_stats_agtests', ['section' => $tab['section']]),
                            'is_active' => $tab['section'] === $section,
                        ];
                }, [
                    ['title' => 'Prehľad', 'section' => '',],
                    ['title' => 'Po dňoch', 'section' => 'by-day',],
                    ['title' => 'Po dňoch a krajoch', 'section' => 'by-day-and-region',],
                    ['title' => 'Po dňoch a okresoch', 'section' => 'by-day-and-district',],
                ])
            ];

            switch ($section) {
                case 'by-day':
                    $data = ['stats' => $agTest->dailyStats(),] + $data;
                    break;
                case 'by-day-and-district':
                    $data = ['stats' => $agTest->districtualDailyStats(new DateTimeImmutable('1 week ago')),] + $data;
                    break;
                case 'by-day-and-region':
                    $data = ['stats' => $agTest->regionalDailyStats(new DateTimeImmutable('2 months ago')),] + $data;
                    break;
                default:
                    $data = $data + ['stats' => $agTest->updateStats()];
            }

            return $this->render('AgTests/index.html.twig', $data);
        });
    }
}
