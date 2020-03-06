<?php

declare(strict_types = 1);

namespace App\Controller\User;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/user", name="app_user_")
 * @IsGranted("ROLE_USER")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class UserController extends AbstractController
{
    /**
     * Dashboard page after connexion.
     * @todo    Add all useful data for a dashboard index (only for users).
     *
     * @Route("/dashboard", name="dashboard", methods={"GET"})
     */
    public function dashboard(): Response
    {
        if ($this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('user/dashboard.html.twig');
    }

    /**
     * @Route("/{uuid<^.{36}$>}", name="show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }
}
