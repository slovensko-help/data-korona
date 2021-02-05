<?php

namespace App\Client\PowerBi;

use App\QueryBuilder\Hint\PaginationHintInterface;
use App\QueryBuilder\PowerBiQueryBuilder;
use App\QueryResult\PowerBiQueryResult;
use App\Service\Content;
use App\Tool\ArrayChain;
use Exception;
use Generator;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

abstract class AbstractClient extends \App\Client\AbstractClient
{
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
        }, self::ONE_DAY);
    }

    protected function createQueryBuilder()
    {
        return new PowerBiQueryBuilder($this->modelId(), $this->datasetId(), $this->reportId());
    }

    protected function all(PowerBiQueryBuilder $queryBuilder, ?PaginationHintInterface $paginationHint = null): Generator
    {
        if ($paginationHint instanceof PaginationHintInterface) {
            $queryBuildersGenerator = $paginationHint->queryBuildersGenerator($queryBuilder);

            $lastItem = null;
            do {
                $pageQueryBuilder = $queryBuildersGenerator->send($lastItem);
                $lastItem = null;

                if ($pageQueryBuilder instanceof PowerBiQueryBuilder) {
                    foreach ($this->execute($pageQueryBuilder)->items() as $item) {
                        yield $item;
                    }

                    if (isset($item)) {
                        $lastItem = $item;
                    }
                }

            } while (null !== $lastItem);
        } else {
            yield from $this->execute($queryBuilder);
        }
    }

    protected function execute(PowerBiQueryBuilder $queryBuilder): PowerBiQueryResult
    {
        return new PowerBiQueryResult($this->cachedApiContent($this->queryUrl(), $queryBuilder->build()));
        //return new PowerBiQueryResult($this->apiContent($this->queryUrl(), $queryBuilder->build()));
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

    private function originUrl(): string
    {
        return $this->baseUrl(static::REPORT_URL);
    }

    private function apiContent(string $url, ?array $formData = null, $secondAttempt = false)
    {
        try {
            return json_decode($this->content->load(
                $url,
                null === $formData ? null : [
                    'json' => $formData
                ],
                [
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

    private function cachedApiContent($url, $formData)
    {
        return $this->cached(md5($url . json_encode($formData)), function () use ($url, $formData) {
            return $this->apiContent($url, $formData);
        }, self::FIVE_MINUTES);
    }

    private function modelsAndExploration()
    {
        $url = $this->apiBaseUrl() . '/public/reports/' . $this->resourceKey() . '/modelsAndExploration?preferReadOnlySession=true';
        return $this->cached(md5($url), function () use ($url) {
            return $this->apiContent($url);
        }, self::ONE_DAY);
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
            return $this->content->load(static::REPORT_URL);
        });
    }
}