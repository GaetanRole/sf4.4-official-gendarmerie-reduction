<?php

/**
 * Admin Controller File
 *
 * @category    Admin
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * Admin home page
     *
     * @todo Add all useful data for admin index
     *
     * @Route("/", name="app_admin_index", methods={"GET"})
     * @return     Response A Response instance
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
