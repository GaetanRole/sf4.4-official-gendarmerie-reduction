<?php

declare(strict_types=1);

namespace App\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;
use \Exception;
use App\Entity\Category;
use App\Entity\Reduction;
use App\Service\GlobalClock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method  Reduction|null find($id, $lockMode = null, $lockVersion = null)
 * @method  Reduction|null findOneBy(array $criteria, array $orderBy = null)
 * @method  Reduction[]    findAll()
 * @method  Reduction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class ReductionRepository extends ServiceEntityRepository
{
    /** @var GlobalClock */
    private $clock;

    public function __construct(ManagerRegistry $registry, GlobalClock $clock)
    {
        $this->clock = $clock;

        parent::__construct($registry, Reduction::class);
    }

    /**
     * Find last active articles.
     *
     * @todo    Add PagerFanta
     *
     * @throws  Exception Datetime Exception
     */
    public function findLatestBy(Category $category = null, $limit = null): Paginator
    {
        $qb = $this->createQueryBuilder('r')
            ->addSelect('u', 'c', 'i')
            ->innerJoin('r.user', 'u')
            ->leftJoin('r.categories', 'c')
            ->leftJoin('r.image', 'i')
            ->andWhere('r.createdAt <= :now')
            ->andWhere('r.isActive = :status')
            ->orderBy('r.createdAt', 'DESC')
            ->setParameter('now', $this->clock->getNowInDateTime())
            ->setParameter('status', true);

        if (null !== $category) {
            $qb->andWhere(':category MEMBER OF r.categories')
                ->setParameter('category', $category);
        }

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        return new Paginator($qb);
    }

    /**
     * Count each Reduction rows with a status parameter.
     *
     * @throws NoResultException        If the query returned no result.
     * @throws NonUniqueResultException If the query result is not unique.
     */
    public function countReductionByStatus(bool $status)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->andWhere('r.isActive = :status')
            ->setParameter('status', $status);

        return $qb
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @return  Reduction[]
     */
    public function findBySearchQuery(string $rawQuery, int $limit = Reduction::NUM_ITEMS): array
    {
        $query = $this->sanitizeSearchQuery($rawQuery);
        $searchTerms = $this->extractSearchTerms($query);

        if (0 === count($searchTerms)) {
            return [];
        }

        $queryBuilder = $this->createQueryBuilder('r');
        foreach ($searchTerms as $key => $term) {
            $queryBuilder
                ->orWhere('r.brand LIKE :c_'.$key)
                ->setParameter('c_'.$key, '%'.$term.'%');
        }

        return $queryBuilder
            ->orderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Removes all non-alphanumeric characters except whitespaces.
     */
    private function sanitizeSearchQuery(string $query): string
    {
        return trim(preg_replace('/[[:space:]]+/', ' ', $query));
    }

    /**
     * Splits the search query into terms and removes the ones which are irrelevant.
     */
    private function extractSearchTerms(string $searchQuery): array
    {
        $terms = array_unique(explode(' ', $searchQuery));
        return array_filter($terms, static function ($term) {
            return 2 <= mb_strlen($term);
        });
    }
}
