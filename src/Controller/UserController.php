<?php

/**
 * User Controller File
 *
 * PHP Version 7.2
 *
 * @category    User
 * @package     App\Controller
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;

/**
 * User Controller Class
 *
 * @category    User
 * @package     App\Controller
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 *
 * @Route("/{_locale}/user", defaults={"_locale"="%locale%"})
 * @IsGranted("ROLE_USER")
 */
class UserController extends AbstractController
{
    /**
     * Find and display an user
     *
     * @param User $user User given by an id
     *
     * @Route("/{id<\d+>}", methods={"GET"})
     * @return     Response A Response instance
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }
}
