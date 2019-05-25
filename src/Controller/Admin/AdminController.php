<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="app_admin_")
 * @IsGranted("ROLE_ADMIN")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class AdminController extends AbstractController
{
    /**
     * @todo    Add all useful data for admin index.
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
