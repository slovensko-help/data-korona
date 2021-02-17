<?php

namespace App\Controller\Front;

use App\Controller\AbstractController;
use App\Entity\Log;
use App\Service\Vaccination;
use Doctrine\DBAL\Connection;
use Symfony\Component\Cache\CacheItem;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class EhranicaController extends AbstractController
{
    /**
     * @Route("/ehranica/", methods={"GET"})
     */
    public function index(Connection $connection, CacheInterface $cache)
    {
            return $this->render('Ehranica/index.html.twig', [
            'days' => $this->days($connection, $cache),
            'weeks' => $this->weeks($connection, $cache),
        ]);
    }

    private function days(Connection $connection, CacheInterface $cache)
    {
        return $cache->get('ehranica-days', function(CacheItem $item) use ($connection) {
            $item->expiresAfter(60);

            return $connection->query("
                SELECT
                    DATE(created_at) AS day,
                    SUM(IF(level = 'WARNING', 1, 0)) + SUM(IF(level = 'CRITICAL' AND message LIKE '%nczisk%', 1, 0)) AS fail_count,
                    SUM(IF(level = 'OK', 1, 0)) AS success_count
                FROM
                    log
                GROUP BY
                    day DESC")->fetchAll();
        });
    }

    private function weeks(Connection $connection, CacheInterface $cache)
    {
        return $cache->get('ehranica-weeks', function(CacheItem $item) use ($connection) {
            $item->expiresAfter(60);

            return $connection->query("
                SELECT
                    DATE(DATE_ADD(created_at, INTERVAL - WEEKDAY(created_at) DAY)) AS week,
                    SUM(IF(level = 'WARNING', 1, 0)) + SUM(IF(level = 'CRITICAL' AND message LIKE '%nczisk%', 1, 0)) AS fail_count,
                    SUM(IF(level = 'OK', 1, 0)) AS success_count
                FROM
                    log
                GROUP BY
                    week DESC")->fetchAll();
        });
    }
}