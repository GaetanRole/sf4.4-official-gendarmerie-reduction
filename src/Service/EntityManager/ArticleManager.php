<?php

declare(strict_types=1);

namespace App\Service\EntityManager;

use App\Entity\Article;
use App\Entity\User;
use EasySlugger\SeoSlugger;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ArticleManager
{
    /**
     * Prepare all articles entities before persist.
     */
    public function prepare(Article $article, User $user): Article
    {
        $article->setSlug(SeoSlugger::uniqueSlugify($article->getTitle()));
        $article->setUser($user);

        return $article;
    }

    /**
     * Keep the last Article slug (instead of making a new one).
     */
    public function handleSlug(Article $article, string $oldTitle): Article
    {
        $currentArticleTitle = $article->getTitle();

        if ($oldTitle !== $currentArticleTitle) {
            $article->setSlug(SeoSlugger::uniqueSlugify($currentArticleTitle));
        }

        return $article;
    }

    /**
     * Enable/Disable an Article.
     */
    public function changeStatus(Article $article): Article
    {
        $article->isActive() ? $article->setIsActive(false) : $article->setIsActive(true);

        return $article;
    }
}
