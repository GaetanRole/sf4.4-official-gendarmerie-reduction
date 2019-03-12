<?php

/**
 * Security Controller File
 *
 * PHP Version 7.2
 *
 * @category    Security
 * @package     App\Controller
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Security Controller
 *
 * @todo Check best practices on this one
 *
 * @category    Security
 * @package     App\Controller
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class SecurityController extends AbstractController
{
    /**
     * Login method
     *
     * @param AuthenticationUtils $authenticationUtils get last Auth
     * @param Security $security Security injection
     *
     * @Route("/login", name="app_login")
     * @return          RedirectResponse|Response A Response instance
     */
    public function login(
        AuthenticationUtils $authenticationUtils,
        Security $security
    ): Response {
        if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash(
                'danger',
                'Vous ne pouvez pas accéder à la page de connexion, étant déjà connecté.'
            );
            return $this->redirectToRoute('app_index');
        }

        return $this->render(
            'security/login.html.twig',
            [
                'last_username' => $authenticationUtils->getLastUsername(),
                'error' => $authenticationUtils->getLastAuthenticationError()
            ]
        );
    }

    /**
     * Logout method
     *
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        // This point is never reached !
    }
}
