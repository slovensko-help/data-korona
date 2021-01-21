<?php

namespace App\Controller;

use App\Repository\NcziMorningEmailRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Surové dáta")
 */
class RawDataController extends AbstractController
{
    /**
     * @Route("/raw/api/nczi-morning-emails", methods={"GET"})
     *
     * @param NcziMorningEmailRepository $repository
     * @param Request $request
     * @return Response
     */
    public function hospitalPatients(NcziMorningEmailRepository $repository, Request $request)
    {
        return $this->paginatedResponse($repository, $request);
    }
}