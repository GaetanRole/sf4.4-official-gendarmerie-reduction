<?php

declare(strict_types=1);

namespace App\Controller\Admin;

use App\Repository\OpinionRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller for the default Admin dashboard linking to other admin controllers related to business logic.
 * Statistics or Admin features can be added here.
 *
 * @see \App\Controller\Article\AdminController
 * @see \App\Controller\Brand\AdminController
 * @see \App\Controller\Category\AdminController
 * @see \App\Controller\Opinion\AdminController
 * @see \App\Controller\Reduction\AdminController
 * @see \App\Controller\User\AdminController
 *
 * @Route("/admin", name="app_admin_")
 *
 * @IsGranted("ROLE_ADMIN")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class DashboardController extends AbstractController
{
    /**
     * @todo    Add all useful data for admin index.
     *
     * @Route("/dashboard", name="dashboard", methods="GET")
     */
    public function dashboard(OpinionRepository $opinionRepository): Response
    {
        return $this->render('admin/dashboard.html.twig', [
            'opinions' => $opinionRepository->findBy([], ['id' => 'DESC'], OpinionRepository::PAGE_SIZE)
        ]);
    }
}
