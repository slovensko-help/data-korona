<?php

namespace App\Controller;

use App\Service\Content;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NcziProxyController extends AbstractController
{
    const ALLOWED_REQUESTS = [
        // vaccinations
        'get_vaccination_groups' => [
            'origin_url' => 'https://mojeezdravie.nczisk.sk/api/v1/web/get_vaccination_groups',
            'allowed_parameters' => [],
        ],
        'get_driveins_vacc' => [
            'origin_url' => 'https://mojeezdravie.nczisk.sk/api/v1/web/get_driveins_vacc',
            'allowed_parameters' => [],
        ],
        'validate_drivein_times_vacc' => [
            'origin_url' => 'https://mojeezdravie.nczisk.sk/api/v1/web/validate_drivein_times_vacc',
            'allowed_parameters' => [
                'drivein_id' => [
                    'required' => true,
                    'type' => 'int',
                ],
            ],
        ],
        'get_all_drivein_times_vacc' => [
            'origin_url' => 'https://mojeezdravie.nczisk.sk/api/v1/web/get_all_drivein_times_vacc',
            'allowed_parameters' => [],
        ],
        // ag tests
        'mom_ag.json' => [
            'origin_url' => 'https://www.old.korona.gov.sk/mom_ag.json',
            'allowed_parameters' => [],
        ],
        'validate_drivein_times' => [
            'origin_url' => 'https://mojeezdravie.nczisk.sk/api/v1/web/validate_drivein_times',
            'allowed_parameters' => [
                'drivein_id' => [
                    'required' => true,
                    'type' => 'int',
                ],
            ],
        ],
        'get_all_drivein_times' => [
            'origin_url' => 'https://mojeezdravie.nczisk.sk/api/v1/web/get_all_drivein_times',
            'allowed_parameters' => [],
        ],
        'get_driveins' => [
            'origin_url' => 'https://mojeezdravie.nczisk.sk/api/v1/web/get_driveins',
            'allowed_parameters' => [],
        ],
    ];

    private $content;
    private $ncziProxyLogger;

    public function __construct(Content $content, LoggerInterface $ncziProxyLogger)
    {
        $this->content = $content;
        $this->ncziProxyLogger = $ncziProxyLogger;
    }

    /**
     * @Route("/ncziapi/time", methods={"GET"})
     */
    public function ncziApiTime()
    {
        return new Response(date('Y-m-d H:i:s'));
    }

    /**
     * @Route("/ncziapi/{route}", methods={"GET"})
     */
    public function ncziApi(string $route, Request $request)
    {
        $routeConfig = self::ALLOWED_REQUESTS[$route] ?? $this->throwNotFoundException();
        $originUrl = $routeConfig['origin_url'] ?? $this->throwNotFoundException();

        $allowedParameters = $routeConfig['allowed_parameters'] ?? [];

        return
            $this->errorIf(
                $this->notAllowedParameters($request, $allowedParameters),
                'Query parameter(s) not allowed', $request) ??
            $this->errorIf(
                $this->missingParameters($request, $this->requiredParameters($allowedParameters)),
                'Query parameter(s) missing', $request) ??
            $this->errorIf(
                $this->wrongTypeParameters($request, $allowedParameters),
                'Wrong type of query parameter(s)', $request) ??
            $this->ncziResponse($originUrl, $request);
    }

    private function ncziResponse(string $url, Request $request): JsonResponse
    {
        $body = 0 === $request->query->count() ? null : ['json' => $request->query->all(),];
        $this->ncziProxyLogger->info(sprintf('[NCZI PROXY MISS] URL=%s, BODY=%s', $url, json_encode($body)));
        return JsonResponse::fromJsonString($this->content->load($url, $body));
    }

    private function requiredParameters(array $allowedParams): array
    {
        $result = [];

        foreach ($allowedParams as $name => $definition) {
            if (true === $definition['required']) {
                $result[$name] = $definition;
            }
        }

        return $result;
    }

    private function errorIf(array $violatingParameters, string $error, Request $request): ?Response
    {
        if (0 === count($violatingParameters)) {
            return null;
        }

        $this->ncziProxyLogger->info(sprintf('[NCZI PROXY ERROR] URL=%s, ERROR=%s', $request->getUri(), $error));

        return new JsonResponse([
            'success' => false,
            'time' => date('Y-m-d H:i:s'),
            'error' => sprintf('%s: %s', $error, join(',', $violatingParameters)),
        ], Response::HTTP_BAD_REQUEST);
    }

    private function wrongTypeParameters(Request $request, array $allowedParameters): array
    {
        $result = [];

        foreach ($allowedParameters as $name => $definition) {
            $rawValue = $request->query->get($name);

            switch ($definition['type']) {
                case 'int':
                    if (strval($rawValue) !== strval(intval($rawValue))) {
                        $result[] = $name;
                    }
                    break;
                default:
                    $result[] = $name;
            }
        }

        return $result;
    }

    private function notAllowedParameters(Request $request, array $allowedParameters): array
    {
        return array_diff($request->query->keys(), array_keys($allowedParameters));
    }

    private function missingParameters(Request $request, array $requiredParameters): array
    {
        return array_diff(array_keys($requiredParameters), $request->query->keys());
    }

    private function throwNotFoundException()
    {
        throw $this->createNotFoundException();
    }
}