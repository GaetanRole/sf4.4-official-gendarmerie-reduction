<?php

declare(strict_types=1);

namespace App\Repository\Adapter;

use \Exception;
use Ramsey\Uuid\Uuid;
use App\Service\GlobalClock;
use App\Entity\EntityInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use App\Event\SuccessPersistenceNotificationEvent;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * This adapter allows all repositories to have a save, update and delete methods.
 *
 * @see RepositoryAdapterInterface
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class RepositoryAdapter implements RepositoryAdapterInterface
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
    public function save(EntityInterface $entity, string $notificationKey = 'save.flash.success'): EntityInterface
    {
        $entity->setUuid(Uuid::uuid4());
        $entity->setCreatedAt($this->clock->getNowInDateTime());

        $this->entityManager->persist($entity);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new SuccessPersistenceNotificationEvent($notificationKey));
        return $entity;
    }

    /**
     * @throws  Exception Datetime Exception
     */
    public function update(EntityInterface $entity, string $notificationKey = 'update.flash.success'): EntityInterface
    {
        $entity->setUpdatedAt($this->clock->getNowInDateTime());

        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new SuccessPersistenceNotificationEvent($notificationKey));
        return $entity;
    }

    public function delete(EntityInterface $entity, string $notificationKey = 'delete.flash.success'): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();

        $this->eventDispatcher->dispatch(new SuccessPersistenceNotificationEvent($notificationKey));
    }
}
