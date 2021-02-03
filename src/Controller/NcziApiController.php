<?php

namespace App\Controller;

use App\Service\Content;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NcziApiController extends AbstractController
{
    const NCZI_API_BASE_URL = 'https://mojeezdravie.nczisk.sk/api/v1/web/';
    const ALLOWED_REQUESTS = [
        'get_vaccination_groups' => [],
        'get_driveins_vacc' => [],
        'validate_drivein_times_vacc' => ['drivein_id'],
    ];

    /**
     * @Route("/ncziapi/{route}", methods={"GET"})
     */
    public function ncziApi(string $route, Request $request, Content $content)
    {
        $routeParams = self::ALLOWED_REQUESTS[$route] ?? $this->throwNotFoundException();

        return
            $this->errorIfWrongParameters(
                $this->notAllowedParameters($request, $routeParams),
                'Query parameter(s) not allowed') ??
            $this->errorIfWrongParameters(
                $this->missingParameters($request, $routeParams),
                'Query parameter(s) missing') ??
            JsonResponse::fromJsonString(
                $content->load(
                    self::NCZI_API_BASE_URL . $route,
                    0 === $request->query->count() ? null : [
                        'json' => $request->query->all(),
                    ]));
    }

    private function errorIfWrongParameters(array $violatingParameters, string $error): ?Response
    {
        return 0 === count($violatingParameters) ? null : new JsonResponse([
            'success' => false,
            'error' => sprintf('%s: %s', $error, join(',', $violatingParameters)),
        ], Response::HTTP_BAD_REQUEST);
    }

    private function notAllowedParameters(Request $request, array $routeParams): array
    {
        return array_diff($request->query->keys(), $routeParams);
    }

    private function missingParameters(Request $request, array $routeParams): array
    {
        return array_diff($routeParams, $request->query->keys());
    }

    private function throwNotFoundException()
    {
        throw $this->createNotFoundException();
    }
}