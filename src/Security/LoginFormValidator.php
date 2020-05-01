<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Complete the LoginFormAuthenticator and split business logic.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class LoginFormValidator
{
    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var TranslatorInterface */
    private $translator;

    public function __construct(
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder,
        TranslatorInterface $translator
    ) {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
        $this->translator = $translator;
    }

    public function checkCsrfToken($credentials): void
    {
        if (!$this->csrfTokenManager->isTokenValid(new CsrfToken('authenticate', $credentials['csrf_token']))) {
            throw new InvalidCsrfTokenException(
                $this->translator->trans('security.authenticator.user.csrf_token.exception', [], 'exceptions')
            );
        }
    }

    public function checkUser(?User $user): void
    {
        if (!$user) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('security.authenticator.user.authentication.exception', [], 'exceptions')
            );
        }

        if (!$user->isActive()) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('security.authenticator.user.disabled.exception', [], 'exceptions')
            );
        }
    }

    public function checkPassword($credentials, UserInterface $user): void
    {
        if (!$this->passwordEncoder->isPasswordValid($user, $credentials['password'])) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('security.authenticator.user.authentication.exception', [], 'exceptions')
            );
        }
    }
}
