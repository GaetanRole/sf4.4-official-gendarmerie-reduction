<?php

namespace App\Repository;

use Doctrine\Persistence\ManagerRegistry;
use Exception;
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
        parent::__construct($registry, Reduction::class);

        $this->clock = $clock;
    }

    /**
     * Find last articles.
     *
     * @todo    Add PagerFanta
     *
     * @return  Reduction[]
     * @throws  Exception Datetime Exception
     */
    public function findLatest(Category $category = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->addSelect('u', 'c')
            ->innerJoin('r.user', 'u')
            ->leftJoin('r.categories', 'c')
            ->where('r.createdAt <= :now')
            ->orderBy('r.createdAt', 'DESC')
            ->setParameter('now', $this->clock->getNowInDateTime());

        if (null !== $category) {
            $qb->andWhere(':category MEMBER OF r.categories')
                ->setParameter('category', $category);
        }

        return $qb->getQuery()->execute();
    }

    /**
     * Find last articles.
     *
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
