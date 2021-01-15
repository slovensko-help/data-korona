<?php

namespace App\Controller;

use App\Entity\TimeSeries\HospitalBeds;
use App\Entity\Aggregation\DistrictHospitalBeds;
use App\Entity\Aggregation\RegionHospitalBeds;
use App\Entity\Aggregation\SlovakiaHospitalBeds;
use App\Repository\DistrictHospitalBedsRepository;
use App\Repository\HospitalBedsRepository;
use App\Repository\RegionHospitalBedsRepository;
use App\Repository\SlovakiaHospitalBedsRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Lôžka")
 */
class HospitalBedsController extends AbstractController
{
    /**
     * Kapacita a obsadenosť rôznych typov lôžok v nemocniciach v časovej rade po dňoch od 30. apríla 2020
     *
     * Záznamy sú aktualizované každý **pracovný** deň okolo obeda a podvečer. Záznamy sú zoradené podľa dňa publikovania (published_on) od najnovších po najstaršie.
     *
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 záznamov na stránku.")
     * @OA\Parameter(name="updated_after", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti stránkovaný zoznam kapacít a obsadenosti lôžok v nemocniciach po dňoch",
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
     *          @OA\Items(ref=@Model(type=HospitalBeds::class))
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
     * @Route("/api/hospital-beds", methods={"GET"})
     *
     * @param HospitalBedsRepository $repository
     * @param Request $request
     * @return Response
     */
    public function hospitalBeds(HospitalBedsRepository $repository, Request $request)
    {
        return $this->paginatedResponse($repository, $request);
    }

    /**
     * Kapacita a obsadenosť rôznych typov lôžok v nemocniciach súhrnne za okresy v časovej rade po dňoch od 30. apríla 2020
     *
     * Záznamy sú aktualizované každý **pracovný** deň okolo obeda a podvečer. Záznamy sú zoradené podľa dňa publikovania (published_on) od najnovších po najstaršie.
     *
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 záznamov na stránku.")
     * @OA\Parameter(name="updated_after", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti stránkovaný zoznam kapacít a obsadenosti lôžok v nemocniciach súhrnne za okresy po dňoch",
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
     *          @OA\Items(ref=@Model(type=DistrictHospitalBeds::class))
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
     * @Route("/api/district-hospital-beds", methods={"GET"})
     *
     * @param DistrictHospitalBedsRepository $repository
     * @param Request $request
     * @return Response
     */
    public function districtHospitalBeds(DistrictHospitalBedsRepository $repository, Request $request)
    {
        return $this->paginatedResponse($repository, $request);
    }

    /**
     * Kapacita a obsadenosť rôznych typov lôžok v nemocniciach súhrnne po krajoch v časovej rade po dňoch od 30. apríla 2020
     *
     * Záznamy sú aktualizované každý **pracovný** deň okolo obeda a podvečer. Záznamy sú zoradené podľa dňa publikovania (published_on) od najnovších po najstaršie.
     *
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 záznamov na stránku.")
     * @OA\Parameter(name="updated_after", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti stránkovaný zoznam kapacít a obsadenosti lôžok v nemocniciach súhrnne po krajoch po dňoch",
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
     *          @OA\Items(ref=@Model(type=RegionHospitalBeds::class))
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
     * @Route("/api/region-hospital-beds", methods={"GET"})
     *
     * @param RegionHospitalBedsRepository $repository
     * @param Request $request
     * @return Response
     */
    public function regionHospitalBeds(RegionHospitalBedsRepository $repository, Request $request)
    {
        return $this->paginatedResponse($repository, $request);
    }

    /**
     * Kapacita a obsadenosť rôznych typov lôžok v nemocniciach súhrnne za celé Slovensko v časovej rade po dňoch od 30. apríla 2020
     *
     * Záznamy sú aktualizované každý **pracovný** deň okolo obeda a podvečer. Záznamy sú zoradené podľa dňa publikovania (published_on) od najnovších po najstaršie.
     *
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 záznamov na stránku.")
     * @OA\Parameter(name="updated_after", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti stránkovaný zoznam kapacít a obsadenosti lôžok v nemocniciach súhrnne za celé Slovensko po dňoch",
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
     *          @OA\Items(ref=@Model(type=SlovakiaHospitalBeds::class))
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
     * @Route("/api/slovakia-hospital-beds", methods={"GET"})
     *
     * @param SlovakiaHospitalBedsRepository $repository
     * @param Request $request
     * @return Response
     */
    public function slovakiaHospitalBeds(SlovakiaHospitalBedsRepository $repository, Request $request)
    {
        return $this->paginatedResponse($repository, $request);
    }
}