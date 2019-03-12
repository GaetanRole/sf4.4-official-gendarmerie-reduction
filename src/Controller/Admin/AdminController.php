<?php

/**
 * Admin Controller File
 *
 * PHP Version 7.2
 *
 * @category    Admin
 * @package     App\Controller\Admin
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Admin Controller Class
 *
 * @category    Admin
 * @package     App\Controller\Admin
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 *
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
     * @Route("/", methods={"GET"}, name="admin_index")
     * @return     Response A Response instance
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }
}
