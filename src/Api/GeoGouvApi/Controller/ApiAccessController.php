<?php

declare(strict_types = 1);

namespace App\Api\GeoGouvApi\Controller;

use App\Api\GeoGouvApi\GeoClient;
use App\Api\GeoGouvApi\Model\ModelEnum;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/geo", name="api_geo_")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ApiAccessController extends AbstractController
{
    /**
     * @internal    Check AJAX request from views.
     * @return      JsonResponse|string Return an error response or the searchField
     */
    private function isQueryStringInvalid(string $searchQuery, string $searchField)
    {
        if (!$searchQuery || '' === $searchQuery) {
            $response = new JsonResponse(
                "$searchField query string can't be empty.",
                Response::HTTP_UNPROCESSABLE_ENTITY,
            );
            $response->headers->set('Content-Type', 'application/problem+json');
            return $response;
        }

        return $searchField;
    }

    /**
     * @api Ajax calls from reduction/new and reduction/edit views.
     * @Route("/get-municipalities-from-department", name="get_municipalities", methods={"GET"})
     */
    public function getMunicipalitiesAccordingToOneDepartment(Request $request, GeoClient $client): JsonResponse
    {
        $queryValue = $request->query->get('search');
        $response = $this->isQueryStringInvalid($queryValue, ModelEnum::DEPARTMENT_CLASS_NAME);
        return (ModelEnum::DEPARTMENT_CLASS_NAME === $response) ?
            new JsonResponse($client->Municipality()->getAllMunicipalitiesByDepartment($queryValue)) : $response;
    }

    /**
     * @api Ajax calls from reduction/new and reduction/edit views.
     * @Route("/get-departments-from-region", name="get_departments", methods={"GET"})
     */
    public function getDepartmentsAccordingToOneRegion(Request $request, GeoClient $client): JsonResponse
    {
        $queryValue = $request->query->get('search');
        $response = $this->isQueryStringInvalid($queryValue, ModelEnum::REGION_CLASS_NAME);
        return (ModelEnum::REGION_CLASS_NAME === $response) ?
            new JsonResponse($client->Department()->getAllDepartmentsByRegion($queryValue)) : $response;
    }
}
