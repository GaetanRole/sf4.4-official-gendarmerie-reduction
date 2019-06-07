<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/", name="app_default_")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * Dashboard page after connexion.
     * @todo    Add all useful data for a dashboard index (only for users).
     *
     * @Route("/dashboard", name="dashboard", methods={"GET"})
     * @IsGranted("ROLE_USER")
     */
    public function dashboard(): Response
    {
        return $this->render('default/dashboard.html.twig');
    }
}
