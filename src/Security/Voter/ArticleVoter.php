<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Article;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * @todo    Add unit tests for it.
 *
 * @see     https://symfony.com/doc/current/security/voters.html
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ArticleVoter extends Voter
{
    /** @var string Voter action. */
    private const VIEW = 'view';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof Article && self::VIEW === $attribute;
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
     * Check if the user has admin rights to view a disabled article.
     */
    private function canView(Article $article, User $user): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $article->isActive();
    }
}
