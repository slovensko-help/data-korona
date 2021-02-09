<?php

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Entity\TimeSeries\Vaccinations;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Vakcinácie")
 */
class VaccinationsController extends AbstractController
{
    /**
     * Kapacita a obsadenosť rôznych typov lôžok v nemocniciach v časovej rade po dňoch od 30. apríla 2020
     *
     * Záznamy sú aktualizované každý **pracovný** deň okolo obeda a podvečer. Záznamy sú zoradené podľa dňa publikovania (published_on) od najnovších po najstaršie.
     *
     * @OA\Parameter(name="offset", in="query", description="Stránkovanie po 1000 záznamov na stránku.")
     * @OA\Parameter(name="updated_since", in="query", description="Vráti len záznamy aktualizované po tomto čase. Príklad: 2021-01-13 12:34:56")
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
     *          @OA\Items(ref=@Model(type=Vaccinations::class))
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
     * @Route("/new-api/vaccinations", methods={"GET"})
     *
     * @param Request $request
     * @return Response
     */
    public function vaccinations(Request $request)
    {
        return $this->paginatedResponse(Vaccinations::class, $request);
    }
}