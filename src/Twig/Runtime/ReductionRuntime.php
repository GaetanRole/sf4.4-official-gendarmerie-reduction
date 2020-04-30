<?php

declare(strict_types=1);

namespace App\Twig\Runtime;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use App\Repository\ReductionRepository;
use Twig\Extension\RuntimeExtensionInterface;

/**
 * @todo    Add unit tests on it.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ReductionRuntime implements RuntimeExtensionInterface
{
    /** @var ReductionRepository */
    private $repository;

    public function __construct(ReductionRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @throws NoResultException        If the query returned no result.
     * @throws NonUniqueResultException If the query result is not unique.
     */
    public function countUnverifiedReductions()
    {
        return $this->repository->countReductionByStatus(false);
    }
}
