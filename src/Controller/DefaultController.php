<?php

/**
 * Default Controller File
 *
 * PHP Version 7.2
 *
 * @category    Default
 * @package     App\Controller
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Default Controller Class
 *
 * @category    Default
 * @package     App\Controller
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
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
     * @Route("/dashboard", name="dashboard", methods={"GET"})
     * @IsGranted("ROLE_USER")
     * @return     Response A Response instance
     */
    public function dashboard(): Response
    {
        return $this->render('default/dashboard.html.twig');
    }
}
