<?php

/**
 * Opinion Repository File
 *
 * @category    Opinion
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Repository;

use App\Entity\Opinion;
use App\Service\GlobalClock;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Opinion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Opinion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Opinion[]    findAll()
 * @method Opinion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OpinionRepository extends ServiceEntityRepository
{
    /**
     * @var GlobalClock
     */
    private $clock;

    /**
     * OpinionRepository constructor.
     *
     * @param RegistryInterface $registry
     * @param GlobalClock $clock
     */
    public function __construct(RegistryInterface $registry, GlobalClock $clock)
    {
        parent::__construct($registry, Opinion::class);

        $this->clock = $clock;
    }

    /**
     * Find last articles
     *
     * @return Opinion[]
     * @throws \Exception Datetime Exception
     */
    public function findLatest(): array
    {
        $qb = $this->createQueryBuilder('o')
            ->addSelect('u', 'r')
            ->innerJoin('o.user', 'u')
            ->innerJoin('o.reduction', 'r')
            ->where('o.creationDate <= :now')
            ->orderBy('o.creationDate', 'DESC')
            ->setParameter('now', $this->clock->getNowInDateTime());

        return $qb->getQuery()->execute();
    }
}
