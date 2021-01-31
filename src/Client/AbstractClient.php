<?php

namespace App\Client;

use App\QueryBuilder\Hint\PaginationHintInterface;
use App\QueryBuilder\PowerBiQueryBuilder;
use App\QueryResult\PowerBiQueryResult;
use App\Service\Content;
use App\Tool\ArrayChain;
use Exception;
use Generator;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class AbstractClient
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
}