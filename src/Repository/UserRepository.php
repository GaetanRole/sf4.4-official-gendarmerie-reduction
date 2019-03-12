<?php

/**
 * User Repository File
 *
 * PHP Version 7.2
 *
 * @category    User
 * @package     App\Repository
 * @version     1.0
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * User Repository Class
 *
 * @category    User
 * @packagy     App\Repository
 * @author      Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>

 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     *
     * @param RegistryInterface $registry Reference to entity
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }
}
