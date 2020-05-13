<?php

declare(strict_types=1);

namespace App\Service\EntityManager;

use App\Entity\Reduction;
use App\Entity\User;
use EasySlugger\SeoSlugger;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ReductionManager
{
    /**
     * Prepare all reduction entities before persist.
     */
    public function prepare(Reduction $reduction, User $user, ?string $clientIp): Reduction
    {
        $reduction->setSlug(SeoSlugger::uniqueSlugify($reduction->getTitle()));
        $reduction->setUser($user);
        $reduction->setClientIp($clientIp);

        // An admin doesn't need a verification.
        if ($user->isAdmin()) {
            $reduction->setIsActive(true);
        }

        return $reduction;
    }

    /**
     * Keep the last Reduction slug (instead of making a new one).
     */
    public function handleSlug(Reduction $reduction, string $oldTitle): Reduction
    {
        $currentReductionTitle = $reduction->getTitle();

        if ($oldTitle !== $currentReductionTitle) {
            $reduction->setSlug(SeoSlugger::uniqueSlugify($currentReductionTitle));
        }

        return $reduction;
    }

    /**
     * Enable/Disable a Reduction and do not include an Image during the flush.
     */
    public function changeStatus(Reduction $reduction): Reduction
    {
        $reduction->isActive() ? $reduction->setIsActive(false) : $reduction->setIsActive(true);
        $reduction->setImageOutOfContext();

        return $reduction;
    }
}
