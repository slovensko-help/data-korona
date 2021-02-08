<?php

namespace App\Controller\Api;

use App\Controller\AbstractController;
use App\Entity\Hospital;
use App\Entity\City;
use App\Entity\District;
use App\Entity\Region;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Entity")
 */
class EntitiesController extends AbstractController {
    /**
     * Zoznam nemocníc a zdravotníckych zariadení
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti zoznam nemocníc a zdravotníckych zariadení.",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Hospital::class))
     *     )
     * )
     *
     * @Route("/api/hospitals", methods={"GET"})
     *
     * @return Response
     */
    public function hospitals()
    {
        return $this->handleView($this->view($this->getRepository(Hospital::class)->findAll(), 200));
    }

    /**
     * Zoznam miest
     *
     * V zozname nie sú všetky slovenské mestá, ale len tie, v ktorých sú nemocnice, ktoré ošetrujú covid pacientov.
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti zoznam miest",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=City::class))
     *     )
     * )
     *
     * @Route("/api/cities", methods={"GET"})
     *
     * @return Response
     */
    public function cities()
    {
        return $this->handleView($this->view($this->getRepository(City::class)->findAll(), 200));
    }

    /**
     * Zoznam okresov
     *
     * V zozname nie sú všetky slovenské okresy, ale len tie, v ktorých sú nemocnice, ktoré ošetrujú covid pacientov.
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti zoznam okresov",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=District::class))
     *     )
     * )
     *
     * @Route("/api/districts", methods={"GET"})
     *
     * @return Response
     */
    public function districts()
    {
        return $this->handleView($this->view($this->getRepository(District::class)->findAll(), 200));
    }

    /**
     * Zoznam krajov
     *
     * @OA\Response(
     *     response=200,
     *     description="Vráti zoznam krajov",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Region::class))
     *     )
     * )
     *
     * @Route("/api/regions", methods={"GET"})
     *
     * @return Response
     */
    public function regions()
    {
        return $this->handleView($this->view($this->getRepository(Region::class)->findAll(), 200));
    }
}