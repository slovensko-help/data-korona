<?php

namespace App\Controller;

use App\Entity\Raw\NcziMorningEmail;
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
     * @param Request $request
     * @return Response
     */
    public function hospitalPatients(Request $request)
    {
        return $this->paginatedResponse(NcziMorningEmail::class, $request);
    }
}