<?php

declare(strict_types=1);

namespace App\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @todo    Check best practices on this one.
 *
 * @Route(name="app_security_")
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class SecurityController extends AbstractController
{
    private const PRE_REGISTERED_USER = 'utilisateur.facebook';

    /**
     * @Route("/login", name="login")
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

        return $this->render('security/login.html.twig', [
            'username' => $authenticationUtils->getLastUsername() ?: self::PRE_REGISTERED_USER,
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(): void
    {
        // This point is never reached !
    }
}
