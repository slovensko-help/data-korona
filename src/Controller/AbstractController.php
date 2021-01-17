<?php

namespace App\Controller;

use App\Repository\PaginableRepositoryInterface;
use DateTimeImmutable;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AbstractController extends AbstractFOSRestController
{
    protected function paginatedResponse(PaginableRepositoryInterface $repository, Request $request)
    {
        $offset = $request->get('offset', PHP_INT_MAX);
        $rawUpdatedSince = $request->get('updated_since', null);
        $updatedSince = null === $rawUpdatedSince ? null : DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $rawUpdatedSince);

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

        $result = $repository->findOnePage($offset, $updatedSince);

        $nextOffset = count($result) > 0 ? $result[count($result) - 1]->getId() : null;

        return $this->handleView($this->view([
            'success' => true,
            'next_offset' => $nextOffset,
            'page' => $result,
        ], Response::HTTP_OK));
    }
}