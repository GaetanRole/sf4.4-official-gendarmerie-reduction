<?php

declare(strict_types=1);

namespace App\Repository;

use \Exception;
use App\Entity\Opinion;
use App\Entity\Reduction;
use App\Entity\User;
use App\Service\GlobalClock;
use App\Service\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Opinion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Opinion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Opinion[]    findAll()
 * @method Opinion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class OpinionRepository extends ServiceEntityRepository
{
    /** @var int Opinion number per page. */
    private const PAGE_SIZE = 5;

    /** @var GlobalClock */
    private $clock;

    public function __construct(ManagerRegistry $registry, GlobalClock $clock)
    {
        $this->clock = $clock;

        parent::__construct($registry, Opinion::class);
    }

    public function findFirstByReduction(Reduction $reduction, int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('o')
            ->addSelect('u', 'r')
            ->innerJoin('o.user', 'u')
            ->innerJoin('o.reduction', 'r')
            ->andWhere('o.reduction = :reduction')
            ->andWhere('u.isActive = :status')
            ->orderBy('r.createdAt', 'ASC')
            ->setParameter('reduction', $reduction)
            ->setParameter('status', true)
        ;

        return (new Paginator($qb, self::PAGE_SIZE))->paginate($page);
    }

    /**
     * Find self::PAGE_SIZE last opinions for an active user.
     *
     * @throws Exception dateTime Emits Exception in case of an error
     */
    public function findLatestByUser(User $user, $limit = self::PAGE_SIZE): DoctrinePaginator
    {
        $qb = $this->createQueryBuilder('o')
            ->addSelect('u', 'r')
            ->innerJoin('o.user', 'u')
            ->innerJoin('o.reduction', 'r')
            ->andWhere('o.user = :user')
            ->andWhere('u.isActive = :status')
            ->andWhere('r.isActive = :status')
            ->orderBy('r.createdAt', 'DESC')
            ->setParameter('status', true)
            ->setParameter('user', $user)
        ;

        if (null !== $limit) {
            $qb->setMaxResults($limit);
        }

        return new DoctrinePaginator($qb);
    }
}
