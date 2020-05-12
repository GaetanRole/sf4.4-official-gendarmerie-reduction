<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Opinion;
use App\Entity\Reduction;
use App\Service\GlobalClock;
use App\Service\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findFirstBy(Reduction $reduction, int $page = 1): Paginator
    {
        $qb = $this->createQueryBuilder('o')
            ->addSelect('u', 'r')
            ->leftJoin('o.user', 'u')
            ->leftJoin('o.reduction', 'r')
            ->andWhere('o.reduction = :reduction')
            ->orderBy('r.createdAt', 'ASC')
            ->setParameter('reduction', $reduction)
        ;

        return (new Paginator($qb, self::PAGE_SIZE))->paginate($page);
    }
}
