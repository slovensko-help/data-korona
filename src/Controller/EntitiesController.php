<?php

namespace App\Controller;

use App\Entity\Hospital;
use App\Entity\City;
use App\Entity\District;
use App\Entity\Region;
use App\Repository\CityRepository;
use App\Repository\DistrictRepository;
use App\Repository\HospitalRepository;
use App\Repository\RegionRepository;
use Nelmio\ApiDocBundle\Annotation\Model;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(name="Entity")
 */
class EntitiesController extends AbstractFOSRestController {
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
     * @param HospitalRepository $hospitalRepository
     * @return Response
     */
    public function hospitals(HospitalRepository $hospitalRepository)
    {
        return $this->handleView($this->view($hospitalRepository->findAll(), 200));
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
     * @param CityRepository $cityRepository
     * @return Response
     */
    public function cities(CityRepository $cityRepository)
    {
        return $this->handleView($this->view($cityRepository->findAll(), 200));
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
     * @param DistrictRepository $districtRepository
     * @return Response
     */
    public function districts(DistrictRepository $districtRepository)
    {
        return $this->handleView($this->view($districtRepository->findAll(), 200));
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
     * @param RegionRepository $regionRepository
     * @return Response
     */
    public function regions(RegionRepository $regionRepository)
    {
        return $this->handleView($this->view($regionRepository->findAll(), 200));
    }
}