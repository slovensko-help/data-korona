<?php

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Entity\TimeSeries\HospitalPatients;
use App\Entity\Aggregation\DistrictHospitalPatients;
use App\Entity\Aggregation\RegionHospitalPatients;
use App\Entity\Aggregation\SlovakiaHospitalPatients;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Pacienti")
 */
class HospitalPatientsController extends AbstractController
{
    /**
     * Počty pacientov v nemocniciach v časovej rade po dňoch od 30. apríla 2020
     *
     * Záznamy sú aktualizované každý **pracovný** deň okolo obeda a podvečer. Záznamy sú zoradené podľa dňa publikovania (published_on) od najnovších po najstaršie.
     *
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 (v deme 20) záznamov na stránku.")
     * @OA\Parameter(name="updated_since", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti stránkovaný zoznam počtov pacientov v nemocniciach po dňoch",
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
     *          @OA\Items(ref=@Model(type=HospitalPatients::class))
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
     * @Route("/api/hospital-patients", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function hospitalPatients(Request $request)
    {
        return $this->paginatedResponse(HospitalPatients::class, $request);
    }

    /**
     * Počty pacientov v nemocniciach súhrnne po okresoch v časovej rade po dňoch od 30. apríla 2020
     *
     * Záznamy sú aktualizované každý **pracovný** deň okolo obeda a podvečer. Záznamy sú zoradené podľa dňa publikovania (published_on) od najnovších po najstaršie.
     *
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 (v deme 20) záznamov na stránku.")
     * @OA\Parameter(name="updated_since", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti stránkovaný zoznam počtov pacientov v nemocniciach súhrnne po okresoch po dňoch",
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
     *          @OA\Items(ref=@Model(type=DistrictHospitalPatients::class))
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
     * @Route("/api/hospital-patients/by-district", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function districtHospitalPatients(Request $request)
    {
        return $this->paginatedResponse(DistrictHospitalPatients::class, $request);
    }

    /**
     * Počty pacientov v nemocniciach súhrnne za kraje v časovej rade po dňoch od 30. apríla 2020
     *
     * Záznamy sú aktualizované každý **pracovný** deň okolo obeda a podvečer. Záznamy sú zoradené podľa dňa publikovania (published_on) od najnovších po najstaršie.
     *
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 (v deme 20) záznamov na stránku.")
     * @OA\Parameter(name="updated_since", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti stránkovaný zoznam počtov pacientov v nemocniciach súhrnne za kraje po dňoch",
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
     *          @OA\Items(ref=@Model(type=RegionHospitalPatients::class))
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
     * @Route("/api/hospital-patients/by-region", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function regionHospitalPatientsByRegion(Request $request)
    {
        return $this->paginatedResponse(RegionHospitalPatients::class, $request);
    }

    /**
     * Počty pacientov v nemocniciach súhrnne za celé Slovensko v časovej rade po dňoch od 30. apríla 2020
     *
     * Záznamy sú aktualizované každý **pracovný** deň okolo obeda a podvečer. Záznamy sú zoradené podľa dňa publikovania (published_on) od najnovších po najstaršie.
     *
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 (v deme 20) záznamov na stránku.")
     * @OA\Parameter(name="updated_since", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti stránkovaný zoznam počtov pacientov v nemocniciach súhrnne za celé Slovensko po dňoch",
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
     *          @OA\Items(ref=@Model(type=SlovakiaHospitalPatients::class))
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
     * @Route("/api/hospital-patients/in-slovakia", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function hospitalPatientsSlovakia(Request $request)
    {
        return $this->paginatedResponse(SlovakiaHospitalPatients::class, $request);
    }
}