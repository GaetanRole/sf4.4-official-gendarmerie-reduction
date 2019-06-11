<?php

namespace App\Repository\ModelAdapter;

use Exception;
use Ramsey\Uuid\Uuid;
use App\Service\GlobalClock;
use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Common\Persistence\ObjectRepository;

/**
 * This interface signals that all repositories needs a save method according to the business logic.
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class EntityRepositoryAdapter implements EntityRepositoryInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var GlobalClock */
    private $clock;

    public function __construct(EntityManagerInterface $entityManager, GlobalClock $clock)
    {
        $this->entityManager = $entityManager;
        $this->clock = $clock;
    }

    public function getRepository(string $className): ObjectRepository
    {
        return $this->entityManager->getRepository($className);
    }

    /**
     * @throws  Exception Datetime Exception
     */
    public function save(EntityInterface $entity): EntityInterface
    {
        $entity->setUuid(Uuid::uuid4());
        $entity->setCreatedAt($this->clock->getNowInDateTime());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        return $entity;
    }

    /**
     * @throws  Exception Datetime Exception
     */
    public function update(EntityInterface $entity): EntityInterface
    {
        $entity->setUpdatedAt($this->clock->getNowInDateTime());

        $this->entityManager->flush();
        return $entity;
    }

    public function delete(EntityInterface $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}
