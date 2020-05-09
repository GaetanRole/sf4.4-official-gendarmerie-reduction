<?php

declare(strict_types=1);

namespace App\Service;

use \ArrayIterator;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Doctrine\ORM\Tools\Pagination\CountWalker;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * Based on Doctrine paginator.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Paginator
{
    /** @var int Default value for items by page. */
    private const PAGE_SIZE = 10;

    /** @var DoctrineQueryBuilder */
    private $queryBuilder;

    /** @var int Items by page. */
    private $pageSize;

    /** @var int */
    private $currentPage;

    /** @var ArrayIterator */
    private $results;

    /** @var int */
    private $numResults;

    public function __construct(DoctrineQueryBuilder $queryBuilder, int $pageSize = self::PAGE_SIZE)
    {
        $this->queryBuilder = $queryBuilder;
        $this->pageSize = $pageSize;
    }

    public function paginate(int $page = 1): self
    {
        $this->currentPage = max(1, $page);

        $query = $this->queryBuilder
            ->setFirstResult(($this->currentPage - 1) * $this->pageSize)
            ->setMaxResults($this->pageSize)
            ->getQuery()
        ;

        if (0 === \count($this->queryBuilder->getDQLPart('join'))) {
            $query->setHint(CountWalker::HINT_DISTINCT, false);
        }

        $paginator = new DoctrinePaginator($query, true);
        $paginator->setUseOutputWalkers(\count($this->queryBuilder->getDQLPart('having') ?: []) > 0);

        $this->results = $paginator->getIterator();
        $this->numResults = $paginator->count();

        return $this;
    }

    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function getLastPage(): int
    {
        return (int) ceil($this->numResults / $this->pageSize);
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function hasPreviousPage(): bool
    {
        return $this->currentPage > 1;
    }

    public function getPreviousPage(): int
    {
        return max(1, $this->currentPage - 1);
    }

    public function hasNextPage(): bool
    {
        return $this->currentPage < $this->getLastPage();
    }

    public function getNextPage(): int
    {
        return min($this->getLastPage(), $this->currentPage + 1);
    }

    public function hasToPaginate(): bool
    {
        return $this->numResults > $this->pageSize;
    }

    public function getNumResults(): int
    {
        return $this->numResults;
    }

    public function getResults(): \Traversable
    {
        return $this->results;
    }
}
