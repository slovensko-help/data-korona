<?php

namespace App\Controller\Api\Raw;

use App\Controller\AbstractController;
use App\Entity\Aggregation\SlovakiaVaccinatedPeople;
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
    public function ncziMorningEmails(Request $request)
    {
        return $this->paginatedResponse(NcziMorningEmail::class, $request);
    }

    /**
     * @Route("/raw/api/vaccinated-people", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function vaccinatedPeople(Request $request)
    {
        return $this->paginatedResponse(SlovakiaVaccinatedPeople::class, $request);
    }
}
