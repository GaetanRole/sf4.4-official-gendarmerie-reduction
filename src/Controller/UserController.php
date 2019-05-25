<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/user", name="app_user_")
 * @IsGranted("ROLE_USER")
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class UserController extends AbstractController
{
    /**
     * @Route("/{uuid<^.{36}$>}", name="show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', ['user' => $user]);
    }
}
