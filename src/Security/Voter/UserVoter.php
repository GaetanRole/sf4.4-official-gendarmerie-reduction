<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @see     https://symfony.com/doc/current/security/voters.html
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class UserVoter extends Voter
{
    /** @var string Voter actions */
    private const EDIT = 'edit';
    private const STATUS = 'status';
    private const DELETE = 'delete';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof User && \in_array($attribute, [self::EDIT, self::DELETE, self::STATUS], true);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $userSubject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof User || $userSubject->hasRole('ROLE_SUPER_ADMIN')) {
            return false;
        }

        if ($userSubject->hasRole('ROLE_ADMIN')) {
            return $currentUser->hasRole('ROLE_SUPER_ADMIN');
        }

        return true;
    }
}
