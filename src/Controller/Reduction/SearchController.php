<?php

declare(strict_types=1);

namespace App\Controller\Reduction;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/reduction/search", name="app_reduction_search_")
 * @IsGranted("ROLE_USER")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class SearchController extends AbstractController
{
    /**
     * @Route(name="handle", methods={"GET"})
     */
    public function handle(Request $request): Response
    {
        dd($request->query);

        /*
         * if (locale)
         *     return $this->render(repo->findBySearchLocaleQuery($))
         * if (brand)
         *     return $this->render(repo->findBySearchBrandQuery($))
         * */
        return new Response();
    }
}
