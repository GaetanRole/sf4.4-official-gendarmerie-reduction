<?php

namespace App\Api\GeoApiGouv\Controller;

use App\Api\GeoApiGouv\GeoClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/geo", name="api_geo_")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class ApiAccessController extends AbstractController
{
    /**
     * @internal    Check AJAX request from views.
     * @return      JsonResponse|string Return an error response or the searchField
     */
    private function isQueryStringInvalid(string $searchQuery, string $searchField)
    {
        if (!$searchQuery || $searchQuery === '') {
            $response = new JsonResponse(
                "$searchField query string can't be empty.",
                Response::HTTP_UNPROCESSABLE_ENTITY
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
        $response = $this->isQueryStringInvalid($queryValue, 'Department');
        return ($response === 'Department') ?
            new JsonResponse($client->Municipality()->getAllMunicipalitiesByDepartment($queryValue)) : $response;
    }

    /**
     * @api Ajax calls from reduction/new and reduction/edit views.
     * @Route("/get-departments-from-region", name="get_departments", methods={"GET"})
     */
    public function getDepartmentsAccordingToOneRegion(Request $request, GeoClient $client): JsonResponse
    {
        $queryValue = $request->query->get('search');
        $response = $this->isQueryStringInvalid($queryValue, 'Region');
        return ($response === 'Region') ?
            new JsonResponse($client->Department()->getAllDepartmentsByRegion($queryValue)) : $response;
    }
}
