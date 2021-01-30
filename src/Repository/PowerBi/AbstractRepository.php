<?php

namespace App\Repository\PowerBi;

use App\QueryBuilder\PowerBiQueryBuilder;
use App\QueryResult\PowerBiQueryResult;
use App\Repository\AbstractRemoteRepository;
use App\Tool\ArrayChain;
use Exception;
use Symfony\Contracts\Cache\ItemInterface;

class AbstractRepository extends AbstractRemoteRepository
{
    const CACHE_TTL = 3600;
    const DAY_IN_SECONDS = 86400;
    const REPORT_URL = null;

    public function schema()
    {
        return $this->cached('schema', function () {
            $response = $this->apiContent($this->apiBaseUrl() . '/public/reports/conceptualschema');

            $result[] = ['Entity', 'Property', 'Type'];

            foreach (ArrayChain::value($response, 'schemas') as $schema) {
                foreach ($schema['schema']['Entities'] as $entity) {
                    if (empty($entity['Private']) && false === strpos($entity['Name'], 'LocalDateTable')) {
                        foreach ($entity['Properties'] as $property) {
                            $result[] = [
                                $entity['Name'],
                                $property['Name'],
                                PowerBiQueryResult::PROPERTY_TYPE_NAMES[$property['DataType']] ?? $property['DataType'],
                            ];
                        }
                    }
                }
            }

            return $result;
        }, self::DAY_IN_SECONDS);
    }

    protected function createQueryBuilder()
    {
        return new PowerBiQueryBuilder($this->modelId(), $this->datasetId(), $this->reportId());
    }

    protected function execute(PowerBiQueryBuilder $queryBuilder): PowerBiQueryResult
    {
        return new PowerBiQueryResult(
            $this->apiContent($this->queryUrl(), $queryBuilder->build()),
            $queryBuilder
        );
    }

    private function modelId(): int
    {
        return (int)ArrayChain::value($this->modelsAndExploration(), 'models', 0, 'id');
    }

    private function datasetId(): string
    {
        return ArrayChain::value($this->modelsAndExploration(), 'exploration', 'report', 'objectId');
    }

    private function reportId(): string
    {
        return ArrayChain::value($this->modelsAndExploration(), 'models', 0, 'dbName');
    }

    private function apiContent(string $url, ?array $formData = null, $secondAttempt = false)
    {
        try {
            return json_decode($this->fileContent(
                $url,
                null === $formData ? $formData : json_encode($formData),
                [
                    'Content-Type' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 11_1_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/88.0.4324.96 Safari/537.36',
                    'Origin' => $this->originUrl(),
                    'X-PowerBI-ResourceKey' => $this->resourceKey(),
                ]), true);
        } catch (Exception $exception) {
            if (!$secondAttempt && strpos($exception->getMessage(), 'Method Not Allowed')) {
                return $this->apiContent($url, $formData, true);
            }

            throw $exception;
        }
    }

    private function modelsAndExploration()
    {
        $url = $this->apiBaseUrl() . '/public/reports/' . $this->resourceKey() . '/modelsAndExploration?preferReadOnlySession=true';
        return $this->cached(md5($url), function () use ($url) {
            return $this->apiContent($url);
        }, self::DAY_IN_SECONDS);
    }

    private function originUrl()
    {
        $urlParts = parse_url(static::REPORT_URL);
        return $urlParts['scheme'] . '://' . $urlParts['host'] . '/';
    }

    private function resourceKey(): string
    {
        return $this->runtimeClassConstant('resourceKey', function () {
            if (null === static::REPORT_URL) {
                throw new Exception('PowerBI REPORT_URL cannot be null.');
            }

            parse_str(parse_url(static::REPORT_URL, PHP_URL_QUERY), $query);

            if (!isset($query['r'])) {
                throw new Exception('PowerBI REPORT_URL must have "r" query parameter.');
            }

            $keys = json_decode(base64_decode($query['r']), true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception(json_last_error_msg());
            }

            if (!isset($keys['k'])) {
                throw new Exception('PowerBI REPORT_URL query structure must contain "k" property.');
            }

            return $keys['k'];
        });
    }

    private function cached(string $name, callable $valueCallback, ?int $cacheTtl = null)
    {
        return $this->cache->get('PowerBiRepositoryCache--' . $name, function (ItemInterface $item) use ($valueCallback, $cacheTtl) {
            $item->expiresAfter($cacheTtl ?? static::CACHE_TTL);
            return $valueCallback();
        });
    }

    private function queryUrl(): string
    {
        preg_match('/queryDataUrl *= *[\'|"](.*)[\'|"]/', $this->reportHtmlContent(), $matches);

        if (!isset($matches[1])) {
            throw new Exception('queryDataUrl is not present in report HTML.');
        }

        return $this->apiBaseUrl() . '/' . ltrim($matches[1], '/') . '?synchronous=true';
    }

    private function apiBaseUrl(): string
    {
        return $this->runtimeClassConstant('apiBaseUrl', function () {
            preg_match('/resolvedClusterUri *= *[\'|"](.*?)[\'|"]/', $this->reportHtmlContent(), $matches);

            if (!isset($matches[1])) {
                throw new Exception('resolvedClusterUri is not present in report HTML.');
            }

            $parts = explode('.', $matches[1]);
            $parts[0] = str_replace('-redirect', '', $parts[0]);
            $parts[0] = str_replace('global-', '', $parts[0]);
            $parts[0] .= '-api';

            return rtrim(implode('.', $parts), '/');
        });
    }

    private function reportHtmlContent()
    {
        return $this->cached(md5(static::REPORT_URL), function () {
            return $this->fileContent(static::REPORT_URL);
        });
    }

    private function runtimeClassConstant(string $name, callable $valueCallback)
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