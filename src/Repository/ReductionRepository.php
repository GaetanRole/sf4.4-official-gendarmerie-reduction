<?php

declare(strict_types=1);

namespace App\Repository;

use \Exception;
use App\Entity\Category;
use App\Entity\Reduction;
use App\Service\GlobalClock;
use App\Service\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Reduction|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reduction|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reduction[]    findAll()
 * @method Reduction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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
     * @throws Exception dateTime Emits Exception in case of an error
     */
    public function findLatestBy(Category $category = null, $limit = null): DoctrinePaginator
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
            ->setParameter('status', true)
        ;

        if (null !== $category) {
            $qb->andWhere(':category MEMBER OF r.categories')
                ->setParameter('category', $category)
            ;
        }

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        return new DoctrinePaginator($qb);
    }

    /**
     * Count each Reduction rows with a status parameter.
     *
     * @throws NoResultException        if the query returned no result
     * @throws NonUniqueResultException if the query result is not unique
     */
    public function countReductionByStatus(bool $status)
    {
        $qb = $this->createQueryBuilder('r')
            ->select('count(r.id)')
            ->andWhere('r.isActive = :status')
            ->setParameter('status', $status)
        ;

        return $qb
            ->getQuery()
            ->getSingleScalarResult()
            ;
    }

    /**
     * @throws Exception dateTime Emits Exception in case of an error
     */
    public function findByLocation(array $locationParameters, int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('r')
            ->addSelect('b', 'c', 'i')
            ->leftJoin('r.brand', 'b')
            ->leftJoin('r.categories', 'c')
            ->leftJoin('r.image', 'i')
            ->andWhere('r.isActive = :status')
            ->orderBy('r.createdAt', 'DESC')
            ->setParameter('status', true)
        ;

        if ($locationParameters['region']) {
            $qb->andWhere('r.region = :region')
                ->setParameter('region', $locationParameters['region'])
            ;
        }

        if ($locationParameters['department']) {
            $qb->andWhere('r.department = :department')
                ->setParameter('department', $locationParameters['department'])
            ;
        }

        if ($locationParameters['municipality']) {
            $qb->andWhere('r.municipality = :municipality')
                ->setParameter('municipality', $locationParameters['municipality'])
            ;
        }

        return (new Paginator($qb))->paginate($page);
    }
}
