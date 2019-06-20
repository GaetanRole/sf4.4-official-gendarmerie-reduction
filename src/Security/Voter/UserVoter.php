<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;

/**
 * @see     https://symfony.com/doc/current/security/voters.html
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class UserVoter extends Voter
{
    /** Voter actions */
    private const
        EDIT = 'edit',
        STATUS = 'status',
        DELETE = 'delete';

    /** @var Security */
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof User && in_array($attribute, [self::EDIT, self::DELETE, self::STATUS], true);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $user, TokenInterface $token): bool
    {
        if (!$token->getUser() instanceof User || $user->hasRole('ROLE_SUPER_ADMIN')) {
            return false;
        }

        if ($user->hasRole('ROLE_ADMIN')) {
            return $this->security->isGranted('ROLE_SUPER_ADMIN');
        }

        return true;
    }
}
