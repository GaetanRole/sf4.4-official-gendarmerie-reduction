<?php

/**
 * Default Controller File
 *
 * @category    Default
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DefaultController extends AbstractController
{
    /**
     * Home page before connexion
     *
     * @Route("/", name="app_index", methods={"GET"})
     * @return     Response A Response instance
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * Dashboard page after connexion
     *
     * @todo Add all useful data for a dashboard index (only for users)
     *
     * @Route("/dashboard", name="app_dashboard", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @return     Response A Response instance
     */
    public function dashboard(): Response
    {
        return $this->render('default/dashboard.html.twig');
    }
}
