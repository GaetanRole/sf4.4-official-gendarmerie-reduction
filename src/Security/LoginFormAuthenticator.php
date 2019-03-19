<?php

/**
 * LoginFormAuthenticator File
 *
 * PHP Version 7.2
 *
 * @category    Login
 * @package     App\Security
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Guard\Authenticator\AbstractFormLoginAuthenticator;
use Symfony\Component\Security\Http\Util\TargetPathTrait;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * LoginFormAuthenticator Class
 *
 * @category    Login
 * @package     App\Security
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class LoginFormAuthenticator extends AbstractFormLoginAuthenticator
{
    /**
     * Trait to get (and set) the URL the user last visited before being forced to authenticate.
     */
    use TargetPathTrait;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    /**
     * @var User
     */
    private $user;

    /**
     * LoginFormAuthenticator constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TranslatorInterface $translator Translator injection
     * @param RouterInterface $router
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function __construct(
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        RouterInterface $router,
        CsrfTokenManagerInterface $csrfTokenManager,
        UserPasswordEncoderInterface $passwordEncoder
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->router = $router;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    /**
     * @param Request $request
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return 'app_login' === $request->attributes->get('_route')
            && $request->isMethod('POST');
    }

    /**
     * Get form input
     *
     * @param Request $request
     *
     * @return array|mixed
     */
    public function getCredentials(Request $request)
    {
        $credentials = [
            'username' => $request->request->get('username'),
            'password' => $request->request->get('password'),
            'csrf_token' => $request->request->get('_csrf_token'),
        ];
        $request->getSession()->set(
            Security::LAST_USERNAME,
            $credentials['username']
        );

        return $credentials;
    }

    /**
     * Get current User with login
     *
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return User|object|UserInterface|null
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $token = new CsrfToken('authenticate', $credentials['csrf_token']);

        if (!$this->csrfTokenManager->isTokenValid($token)) {
            throw new InvalidCsrfTokenException(
                $this->translator->trans('security.authenticator.user.csrf_token.exception', [], 'exceptions')
            );
        }

        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $credentials['username']]);

        if (!$user) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('security.authenticator.user.authentication.exception', [], 'exceptions')
            );
        }

        $this->user = $user;
        return $user;
    }

    /**
     * Check if User's password is valid
     *
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user): bool
    {
        $state = $this->passwordEncoder->isPasswordValid($user, $credentials['password']);
        if (false === $state) {
            throw new CustomUserMessageAuthenticationException(
                $this->translator->trans('security.authenticator.user.authentication.exception', [], 'exceptions')
            );
        }
        return $state;
    }

    /**
     * Switch between two routes according to User's role
     *
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     * @return RedirectResponse|\Symfony\Component\HttpFoundation\Response|null
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $providerKey)) {
            return new RedirectResponse($targetPath);
        }

        $roles = array_map(function ($role) {
            return $role->getRole();
        }, $token->getRoles());

        if (in_array('ROLE_ADMIN', $roles, true)
            || in_array('ROLE_SUPER_ADMIN', $roles, true)) {
            return new RedirectResponse($this->router->generate('app_admin_index'));
        }
        return new RedirectResponse($this->router->generate('app_dashboard'));
    }

    /**
     * Getting login URL
     *
     * @return string The generated URL
     */
    protected function getLoginUrl(): string
    {
        return $this->router->generate('app_login');
    }
}
