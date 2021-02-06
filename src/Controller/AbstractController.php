<?php

namespace App\Controller;

use App\Repository\EntityRepository;
use App\Repository\PaginableRepositoryInterface;
use DateTimeImmutable;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AbstractController extends AbstractFOSRestController
{
    /** @var EntityManagerInterface */
    private $entityManager;

    protected function getRepository(string $entityClass): EntityRepository
    {
        $repository = $this->entityManager->getRepository($entityClass);
        if ($repository instanceof EntityRepository) {
            return $repository;
        }

        throw new Exception('Entity needs to be handled by EntityRepository or its descendant.');
    }

    protected function paginatedResponse(string $entityClass, Request $request)
    {
        $offset = $request->get('offset', PHP_INT_MAX);
        $rawUpdatedSince = $request->get('updated_since', null);
        $rawPublishedSince = $request->get('published_since', null);
        $updatedSince = null === $rawUpdatedSince ? null : DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $rawUpdatedSince);
        $publishedSince = null === $rawPublishedSince ? null : DateTimeImmutable::createFromFormat('Y-m-d', $rawPublishedSince)->setTime(0, 0, 0);

        if (false === $publishedSince) {
            $publishedSince = null;
        }

        if (false === $updatedSince) {
            return $this->handleView($this->view([
                'success' => false,
                'code' => 'invalid_value_updated_since',
                'error' => 'Invalid value. Updated_since must be empty or properly formatted. Example: ' . date('Y-m-d H:i:s'),
            ], Response::HTTP_BAD_REQUEST));
        }

        if (!is_numeric($offset)) {
            return $this->handleView($this->view([
                'success' => false,
                'code' => 'invalid_value_offset',
                'error' => 'Invalid value. Offset is not int.',
            ], Response::HTTP_BAD_REQUEST));
        }

        $result = $this->entityManager->getRepository($entityClass)
            ->findOnePage($offset, $updatedSince, $publishedSince);

        $nextOffset = count($result) > 0 ? $result[count($result) - 1]->getId() : null;

        return $this->handleView($this->view([
            'success' => true,
            'next_offset' => $nextOffset,
            'page' => $result,
        ], Response::HTTP_OK));
    }

    /**
     * @required
     * @param EntityManagerInterface $entityManager
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }
}