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
            'reasons' => $this->reasons($connection, $cache),
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
                    day
                ORDER BY
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
                    week
                ORDER BY
                    week DESC")->fetchAll();
        });
    }

    private function reasons(Connection $connection, CacheInterface $cache)
    {
        return $cache->get('ehranica-reasons', function(CacheItem $item) use ($connection) {
            $item->expiresAfter(60);

            $result = $connection->query("
                SELECT
                    message,
                    MAX(created_at) AS last_occurred_at,
                    COUNT(*) AS count
                FROM
                    log
                WHERE
                    level = 'WARNING'
                        AND
                    message LIKE '%SQL error 88%'
                GROUP BY
                    message
                ORDER BY
                    count DESC")->fetchAll();

            $reasons = [];

            foreach ($result as $record) {
                if (preg_match('/^NCZI API Full response: (.*)\. status/', $record['message'], $matches)) {
                    $message = json_decode($matches[1], true);

                    if (is_array($message) && isset($message['errors']) && count($message['errors']) > 0) {
                        $error = $message['errors'][0]['description'];

                        if (!isset($reasons[$error])) {
                            $reasons[$error] = [
                                'error' => $error,
                                'last_occurred_at' => $record['last_occurred_at'],
                                'count' => (int) $record['count'],
                            ];
                        }
                        else {
                            $reasons[$error]['count'] += (int) $record['count'];
                            $reasons[$error]['last_occurred_at'] = max($reasons[$error]['last_occurred_at'], $record['last_occurred_at']);
                        }
                    }
                }
            }

            return $reasons;
        });
    }
}