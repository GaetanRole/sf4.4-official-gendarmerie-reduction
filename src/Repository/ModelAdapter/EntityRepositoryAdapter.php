<?php

declare(strict_types = 1);

namespace App\Repository\ModelAdapter;

use \Exception;
use Ramsey\Uuid\Uuid;
use App\Service\GlobalClock;
use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use App\Event\SuccessPersistenceNotificationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This interface signals that all repositories needs a save method according to the business logic.
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class EntityRepositoryAdapter implements EntityRepositoryInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var EventDispatcherInterface */
    private $eventDispatcher;

    /** @var GlobalClock */
    private $clock;

    public function __construct(
        EntityManagerInterface $entityManager,
        EventDispatcherInterface $eventDispatcher,
        GlobalClock $clock
    ) {
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
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

        $this->eventDispatcher->dispatch(new SuccessPersistenceNotificationEvent('save.flash.success'));
        return $entity;
    }

    /**
     * @throws  Exception Datetime Exception
     */
    public function update(EntityInterface $entity): EntityInterface
    {
        $entity->setUpdatedAt($this->clock->getNowInDateTime());

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new SuccessPersistenceNotificationEvent('update.flash.success'));
        return $entity;
    }

    public function delete(EntityInterface $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new SuccessPersistenceNotificationEvent('delete.flash.success'));
    }
}
