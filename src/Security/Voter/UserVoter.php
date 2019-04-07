<?php

/**
 * User Voter File
 *
 * @category    User
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * @see         https://symfony.com/doc/current/security/voters.html
 */
class UserVoter extends Voter
{
    /**
     * Voter action
     */
    private const EDIT = 'edit';

    /**
     * Voter action
     */
    private const DELETE = 'delete';

    /**
     * Voter action
     */
    private const BANISH = 'banish';

    /**
     * @var Security
     */
    private $security;

    /**
     * UserVoter constructor.
     *
     * @param Security $security
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof User && \in_array($attribute, [self::EDIT, self::DELETE, self::BANISH], true);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $user, TokenInterface $token): bool
    {
        $connectedUser = $token->getUser();

        if (!$connectedUser instanceof User) {
            return false;
        }

        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SUPER_ADMIN')) {
            return $this->security->isGranted('ROLE_SUPER_ADMIN');
        }

        return true;
    }
}
