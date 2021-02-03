<?php

namespace App\Controller;

use App\Service\Content;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NcziApiController extends AbstractController
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
    ];

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
    public function ncziApi(string $route, Request $request, Content $content, LoggerInterface $logger)
    {
        $routeConfig = self::ALLOWED_REQUESTS[$route] ?? $this->throwNotFoundException();
        $originUrl = $routeConfig['origin_url'] ?? $this->throwNotFoundException();

        $allowedParameters = $routeConfig['allowed_parameters'] ?? [];

        return
            $this->errorIf(
                $this->notAllowedParameters($request, $allowedParameters),
                'Query parameter(s) not allowed') ??
            $this->errorIf(
                $this->missingParameters($request, $this->requiredParameters($allowedParameters)),
                'Query parameter(s) missing') ??
            $this->errorIf(
                $this->wrongTypeParameters($request, $allowedParameters),
                'Wrong type of query parameter(s)') ??
            $this->ncziResponse($originUrl, $request, $content, $logger);
    }

    private function ncziResponse(string $url, Request $request, Content $content, LoggerInterface $logger): JsonResponse
    {
        $body = 0 === $request->query->count() ? null : ['json' => $request->query->all(),];
        $logger->info(sprintf('[NCZI API HIT] URL=%s, BODY=%s', $url, json_encode($body)));
        return JsonResponse::fromJsonString($content->load($url, $body));
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

    private function errorIf(array $violatingParameters, string $error): ?Response
    {
        return 0 === count($violatingParameters) ? null : new JsonResponse([
            'success' => false,
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