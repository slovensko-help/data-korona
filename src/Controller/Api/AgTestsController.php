<?php

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Entity\Aggregation\DistrictAgTests;
use App\Entity\Aggregation\RegionAgTests;
use App\Entity\Aggregation\RegionVaccinations;
use App\Entity\Aggregation\SlovakiaAgTests;
use App\Entity\Aggregation\SlovakiaVaccinations;
use App\Entity\TimeSeries\AgTests;
use App\Entity\TimeSeries\Vaccinations;
use App\Entity\VaccinationContacts;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Ag testy")
 */
class AgTestsController extends AbstractController
{
    /**
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 (v deme 20) záznamov na stránku.")
     * @OA\Parameter(name="updated_since", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti stránkovaný zoznam",
     *      @OA\JsonContent(
     *        type="object",
     *        @OA\Property(
     *          property="success",
     *          type="boolean",
     *          default="true"
     *        ),
     *        @OA\Property(
     *          property="next_offset",
     *          type="number",
     *          nullable=true,
     *          title="Offset nasledujúcej strany výsledkov"
     *        ),
     *        @OA\Property(
     *          property="page",
     *          type="array",
     *          @OA\Items(ref=@Model(type=DistrictAgTests::class))
     *        )
     *     )
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Vráti chybu pri nevalidných hodnotách parametrov.",
     *     @OA\JsonContent(
     *        type="object",
     *        @OA\Property(
     *          property="success",
     *          type="boolean",
     *          example="false"
     *        ),
     *        @OA\Property(
     *          property="code",
     *          type="string",
     *          example="invalid_value_offset"
     *        ),
     *        @OA\Property(
     *          property="error",
     *          type="string",
     *          example="Invalid value. Offset is not int."
     *        )
     *     )
     * )
     *
     * @Route("/api/ag-tests/by-district", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function districtAgTests(Request $request)
    {
        return $this->paginatedResponse(DistrictAgTests::class, $request);
    }

    /**
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 (v deme 20) záznamov na stránku.")
     * @OA\Parameter(name="updated_since", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti stránkovaný zoznam",
     *      @OA\JsonContent(
     *        type="object",
     *        @OA\Property(
     *          property="success",
     *          type="boolean",
     *          default="true"
     *        ),
     *        @OA\Property(
     *          property="next_offset",
     *          type="number",
     *          nullable=true,
     *          title="Offset nasledujúcej strany výsledkov"
     *        ),
     *        @OA\Property(
     *          property="page",
     *          type="array",
     *          @OA\Items(ref=@Model(type=RegionAgTests::class))
     *        )
     *     )
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Vráti chybu pri nevalidných hodnotách parametrov.",
     *     @OA\JsonContent(
     *        type="object",
     *        @OA\Property(
     *          property="success",
     *          type="boolean",
     *          example="false"
     *        ),
     *        @OA\Property(
     *          property="code",
     *          type="string",
     *          example="invalid_value_offset"
     *        ),
     *        @OA\Property(
     *          property="error",
     *          type="string",
     *          example="Invalid value. Offset is not int."
     *        )
     *     )
     * )
     *
     * @Route("/api/ag-tests/by-region", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function regionAgTests(Request $request)
    {
        return $this->paginatedResponse(RegionAgTests::class, $request);
    }

    /**
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 (v deme 20) záznamov na stránku.")
     * @OA\Parameter(name="updated_since", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti stránkovaný zoznam",
     *      @OA\JsonContent(
     *        type="object",
     *        @OA\Property(
     *          property="success",
     *          type="boolean",
     *          default="true"
     *        ),
     *        @OA\Property(
     *          property="next_offset",
     *          type="number",
     *          nullable=true,
     *          title="Offset nasledujúcej strany výsledkov"
     *        ),
     *        @OA\Property(
     *          property="page",
     *          type="array",
     *          @OA\Items(ref=@Model(type=SlovakiaAgTests::class))
     *        )
     *     )
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Vráti chybu pri nevalidných hodnotách parametrov.",
     *     @OA\JsonContent(
     *        type="object",
     *        @OA\Property(
     *          property="success",
     *          type="boolean",
     *          example="false"
     *        ),
     *        @OA\Property(
     *          property="code",
     *          type="string",
     *          example="invalid_value_offset"
     *        ),
     *        @OA\Property(
     *          property="error",
     *          type="string",
     *          example="Invalid value. Offset is not int."
     *        )
     *     )
     * )
     *
     * @Route("/api/ag-tests/in-slovakia", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function slovakiaSlovakiaAgTests(Request $request)
    {
        return $this->paginatedResponse(SlovakiaAgTests::class, $request);
    }

    /**
     * Kontaktné údaje na vakcinačné miesta
     *
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 (v deme 20) záznamov na stránku.")
     * @OA\Parameter(name="updated_since", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti stránkovaný zoznam",
     *      @OA\JsonContent(
     *        type="object",
     *        @OA\Property(
     *          property="success",
     *          type="boolean",
     *          default="true"
     *        ),
     *        @OA\Property(
     *          property="next_offset",
     *          type="number",
     *          nullable=true,
     *          title="Offset nasledujúcej strany výsledkov"
     *        ),
     *        @OA\Property(
     *          property="page",
     *          type="array",
     *          @OA\Items(ref=@Model(type=VaccinationContacts::class))
     *        )
     *     )
     * )
     *
     * @OA\Response(
     *     response=400,
     *     description="Vráti chybu pri nevalidných hodnotách parametrov.",
     *     @OA\JsonContent(
     *        type="object",
     *        @OA\Property(
     *          property="success",
     *          type="boolean",
     *          example="false"
     *        ),
     *        @OA\Property(
     *          property="code",
     *          type="string",
     *          example="invalid_value_offset"
     *        ),
     *        @OA\Property(
     *          property="error",
     *          type="string",
     *          example="Invalid value. Offset is not int."
     *        )
     *     )
     * )
     *
     * @Route("/api/vaccination/contacts", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
//    public function vaccinationContacts(Request $request)
//    {
//        return $this->paginatedResponse(VaccinationContacts::class, $request);
//    }
}
