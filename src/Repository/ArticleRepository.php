<?php

declare(strict_types=1);

namespace App\Repository;

use \Exception;
use App\Entity\Article;
use App\Service\GlobalClock;
use App\Service\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    /** @var GlobalClock */
    private $clock;

    public function __construct(ManagerRegistry $registry, GlobalClock $clock)
    {
        $this->clock = $clock;

        parent::__construct($registry, Article::class);
    }

    /**
     * Find last active and important articles with a limit.
     *
     * @throws Exception dateTime Emits Exception in case of an error
     */
    public function findLatestImportant(int $limit = null): DoctrinePaginator
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('u')
            ->innerJoin('a.user', 'u')
            ->andWhere('a.createdAt <= :now')
            ->andWhere('a.isActive = :status')
            ->addOrderBy('a.createdAt', 'DESC')
            ->addOrderBy('a.priority', 'DESC')
            ->setParameter('now', $this->clock->getNowInDateTime())
            ->setParameter('status', true)
        ;

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        return new DoctrinePaginator($qb);
    }

    /**
     * Find last active articles.
     */
    public function findLatestBy(int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('a')
            ->addSelect('u')
            ->innerJoin('a.user', 'u')
            ->andWhere('a.isActive = :status')
            ->orderBy('a.createdAt', 'DESC')
            ->setParameter('status', true)
        ;

        return (new Paginator($qb))->paginate($page);
    }
}
