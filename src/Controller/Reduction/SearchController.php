<?php

declare(strict_types=1);

namespace App\Controller\Reduction;

use \Exception;
use App\Form\SearchType;
use App\Repository\ReductionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Search feature based on location and brand fields.
 *
 * @Route("/reduction/search", name="app_reduction_search_")
 * @IsGranted("ROLE_USER")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class SearchController extends AbstractController
{
    /** @var ReductionRepository */
    private $reductionRepository;

    public function __construct(ReductionRepository $repository)
    {
        $this->reductionRepository = $repository;
    }

    /**
     * Returns an HTML response (appended by Jquery in AJAX).
     *
     * @Route(name="handle", methods="GET")
     *
     * @throws Exception dateTime Emits Exception in case of an error
     */
    public function handle(Request $request): Response
    {
        $reductions = [];

        if ($this->isSent($request) && $this->isValid($request)) {
            $reductions = $this->doSearchRequest($request);
        }

        return $this->render('reduction/_search_results.html.twig', [
            'paginator' => $reductions,
            'pastQueryString' => $this->getPastQueryString($request),
        ]);
    }

    private function isSent(Request $request): bool
    {
        return $request->query->get('search') ? true : false;
    }

    /**
     * @todo isValid has to handle Brand too.
     */
    private function isValid(Request $request): bool
    {
        $queryParameters = $request->query->get('search');

        if (!\array_key_exists('method', $queryParameters)) {
            return false;
        }

        if (!\array_key_exists('page', $queryParameters)) {
            return false;
        }

        return SearchType::SEARCH_METHODS[0] === $queryParameters['method'] &&
            $this->checkLocationFields($queryParameters);
    }

    private function checkLocationFields(array $queryParameters): bool
    {
        $fields = ['region', 'department', 'municipality'];

        foreach ($fields as $field) {
            if (!\array_key_exists($field, $queryParameters)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @todo FindByBrand is not ready yet. Complete this one later.
     *
     * @throws Exception dateTime Emits Exception in case of an error
     */
    private function doSearchRequest(Request $request)
    {
        $queryParameters = $request->query->get('search');
        $page = (int) $queryParameters['page'];
        $method = $queryParameters['method'];

        return SearchType::SEARCH_METHODS[0] === $method ?
            $this->reductionRepository->findByLocation($queryParameters, $page ?? 1) :
            $this->reductionRepository->findBy(
                ['brand' => $queryParameters['brand'], 'isActive' => true],
                ['createdAt' => 'ASC']
            );
    }

    private function getPastQueryString(Request $request): string
    {
        return preg_replace('/&search%5Bpage.*/', '', $request->getQueryString());
    }
}
