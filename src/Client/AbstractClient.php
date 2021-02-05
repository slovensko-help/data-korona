<?php

namespace App\Client;

use App\Entity\District;
use App\QueryBuilder\Hint\PaginationHintInterface;
use App\QueryBuilder\PowerBiQueryBuilder;
use App\QueryResult\PowerBiQueryResult;
use App\Service\Content;
use App\Tool\ArrayChain;
use Exception;
use Generator;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

abstract class AbstractClient
{
    const CACHE_TTL = 3600;
    const ONE_DAY = 86400;
    const FIVE_MINUTES = 300;

    /**
     * @var Content
     */
    protected $content;

    /**
     * @var CacheInterface
     */
    protected $cache;

    protected function dataItems(iterable $dataItems): Generator
    {
        foreach ($dataItems as $dataItem) {
            yield $this->dataItemToEntities($dataItem);
        }
    }

    protected function baseUrl(string $url): string
    {
        $urlParts = parse_url($url);
        return $urlParts['scheme'] . '://' . $urlParts['host'] . '/';
    }

    protected function cached(string $name, callable $valueCallback, ?int $cacheTtl = null)
    {
        return $this->cache->get(str_replace('\\', '|', static::class) . '--ClientCache--' . $name, function (ItemInterface $item) use ($valueCallback, $cacheTtl) {
            $item->expiresAfter($cacheTtl ?? static::CACHE_TTL);
            return $valueCallback();
        });
    }

    protected function runtimeClassConstant(string $name, callable $valueCallback)
    {
        static $classValues;

        if (null === $classValues) {
            $classValues = [];
        }

        $key = static::class . '/' . $name;

        if (!isset($classValues[$key])) {
            $classValues[$key] = $valueCallback();
        }

        return $classValues[$key];
    }

    protected function nullOrInt($stringValue): ?int
    {
        return '' === $stringValue ? null : (int)$stringValue;
    }

    protected function nullOrFloat($stringValue): ?float
    {
        return '' === $stringValue ? null : round((float)str_replace(',', '.', $stringValue), 3);
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
        if ('P99999999999' !== $code) {
            return $code;
        }

        return $code . '_' . substr(sha1($name), 0, 8);
    }

    /**
     * @required
     * @param Content $content
     */
    public function setContent(Content $content)
    {
        $this->content = $content;
    }

    /**
     * @required
     * @param CacheInterface $cache
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;
    }
}