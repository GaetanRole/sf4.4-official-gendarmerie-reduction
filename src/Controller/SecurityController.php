<?php

/**
 * Security Controller File
 *
 * @category    Security
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @todo Check best practices on this one
 */
class SecurityController extends AbstractController
{
    /**
     * Login method
     *
     * @param AuthenticationUtils $authenticationUtils get last Auth
     * @param Security $security Security injection
     * @param TranslatorInterface $translator Translator injection
     *
     * @Route("/login", name="app_login")
     * @return          RedirectResponse|Response A Response instance
     */
    public function login(
        AuthenticationUtils $authenticationUtils,
        Security $security,
        TranslatorInterface $translator
    ): Response {
        if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash(
                'danger',
                $translator->trans('is_authenticated_fully.flash.redirection', [], 'flashes')
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
