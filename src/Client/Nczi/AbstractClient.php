<?php

namespace App\Client\Nczi;

use App\QueryBuilder\Hint\PaginationHintInterface;
use App\QueryBuilder\PowerBiQueryBuilder;
use App\QueryResult\PowerBiQueryResult;
use App\Service\Content;
use App\Tool\ArrayChain;
use Exception;
use Generator;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class AbstractClient extends \App\Client\AbstractClient
{
    const REPORT_URL = 'https://covid-19.nczisk.sk/sk';

    protected function apiContent(string $url, ?array $formData = null, $secondAttempt = false)
    {
        try {
            return json_decode($this->content->load(
                $url,
                null === $formData ? null : [
                    'json' => $formData
                ],
                [
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Safari/537.36',
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Origin' => $this->originUrl(),
                    'X-Xsrf-Token' => $this->xsrfToken(),
                    'Cookie' => $this->cookies(),
                ]), true);
        } catch (Exception $exception) {
            if (!$secondAttempt && strpos($exception->getMessage(), 'Method Not Allowed')) {
                return $this->apiContent($url, $formData, true);
            }

            throw $exception;
        }
    }

    private function xsrfToken(): ?string
    {
        foreach ($this->reportHtmlResponseHeaders()['set-cookie'] as $cookies) {
            preg_match('/([^=]+)=([^;]+)/', $cookies, $matches);

            if ('XSRF-TOKEN' === $matches[1]) {
                return urldecode($matches[2]);
            }
        }

        return null;
    }

    private function cookies(): string
    {
        return join(';', $this->reportHtmlResponseHeaders()['set-cookie']);
    }

    private function reportHtmlResponseHeaders()
    {
        return $this->cached('xsrfToken', function () {
            return $this->content->response(static::REPORT_URL)->getHeaders();
        }, self::FIVE_MINUTES);
    }

    private function originUrl(): string
    {
        return $this->baseUrl(static::REPORT_URL);
    }
}