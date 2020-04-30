<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Twig\Runtime\ReductionRuntime;
use Twig\TwigFunction;
use Twig\Extension\AbstractExtension;

/**
 * @todo    Add unit tests on it.
 * All Twig functions and filters related to Reduction entity.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ReductionExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('unverified_reductions_number', [ReductionRuntime::class, 'countUnverifiedReductions'])
        ];
    }
}
