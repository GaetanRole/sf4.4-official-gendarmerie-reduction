<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @todo    Check best practices on this one.
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     * @return  RedirectResponse|Response A Response instance
     */
    public function login(AuthenticationUtils $authenticationUtils, Security $security, TranslatorInterface $translator)
    {
        if ($security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash(
                'danger',
                $translator->trans('is_authenticated_fully.flash.redirection', [], 'flashes')
            );
            return $this->redirectToRoute('app_index');
        }

        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        // This point is never reached !
    }
}
