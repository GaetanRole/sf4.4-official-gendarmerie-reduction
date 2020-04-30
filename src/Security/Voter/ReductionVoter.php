<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Reduction;
use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * @todo    Add unit tests for it.
 * @see     https://symfony.com/doc/current/security/voters.html
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ReductionVoter extends Voter
{
    /** Voter actions */
    private const VIEW = 'view';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Reduction && self::VIEW === $attribute;
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $currentUser = $token->getUser();

        if (!$currentUser instanceof User) {
            return false;
        }

        return $this->canView($subject, $currentUser);
    }

    /**
     * Check if the user has admin rights to view an unverified reduction
     */
    private function canView(Reduction $reduction, User $user): bool
    {
        if ($user->hasRole('ROLE_ADMIN') || $user->hasRole('ROLE_SUPER_ADMIN')) {
            return true;
        }

        return $reduction->isActive();
    }
}
