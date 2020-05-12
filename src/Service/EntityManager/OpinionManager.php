<?php

declare(strict_types=1);

namespace App\Service\EntityManager;

use App\Entity\Opinion;
use App\Entity\Reduction;
use App\Entity\User;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class OpinionManager
{
    /**
     * Prepare all Opinion entities before persist.
     */
    public function prepare(Opinion $opinion, Reduction $reduction, ?string $clientIp, User $user): Opinion
    {
        $opinion->setReduction($reduction);
        $opinion->setClientIp($clientIp);
        $opinion->setUser($user);

        return $opinion;
    }
}
