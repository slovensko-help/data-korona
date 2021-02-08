<?php

namespace App\Controller\Front;

use App\Controller\AbstractController;
use App\Service\Vaccination;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class StatsController extends AbstractController
{
    const FIVE_MINUTES = 300;

    /**
     * @Route("/stats/vaccinations/{section}", methods={"GET"})
     */
    public function vaccinations(Vaccination $vaccination, CacheInterface $cache, string $section = '')
    {

        return $cache->get('stats-vaccinations-' . $section, function (ItemInterface $item) use ($section, $vaccination) {
            $item->expiresAfter($cacheTtl ?? static::FIVE_MINUTES);

            $data = [
                'section' => $section,
                'tabs' => array_map(function ($tab) use ($section) {
                    return $tab + [
                            'url' => $this->generateUrl('app_front_stats_vaccinations', ['section' => $tab['section']]),
                            'is_active' => $tab['section'] === $section,
                        ];
                }, [
                    ['title' => 'Prehľad', 'section' => '',],
                    ['title' => 'Po dňoch', 'section' => 'by-day',],
                    ['title' => 'Po dňoch a krajoch', 'section' => 'by-day-and-region',],
                ])
            ];

            switch ($section) {
                case 'by-day':
                    $data = $data + ['stats' => $vaccination->dailyStats(),];
                    break;
                case 'by-day-and-region':
                    $data = $data + ['stats' => $vaccination->regionalDailyStats(),];
                    break;
                default:
                    $data = $data + ['stats' => $vaccination->updateStats()];
            }

            return $this->render('Stats/vaccinations.html.twig', $data);
        });
    }
}