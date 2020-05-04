<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Image;
use App\Entity\Reduction;
use App\Service\EntityManager\ImageManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;

/**
 * A Listener handling cached and real image storage.
 * @todo Write unit tests for this listener.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ImageStorageListener
{
    /** @var string */
    private const TARGETED_FILTERS = 'thumbnail';

    /** @var ImageManager */
    private $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;
    }

    /**
     * Removing old cached and real images, before the upload function.
     */
    public function onFlush(OnFlushEventArgs $args): void
    {
        $em = $args->getEntityManager();
        $uow = $em->getUnitOfWork();

        $entities = $uow->getScheduledEntityUpdates();

        if ($entities) {
            /** @var Reduction $entity */
            $entity = $entities[array_key_first($entities)];
            if (!$entity instanceof Reduction) {
                return;
            }

            /** @var Image $image */
            $image = $entity->getImage();

            if (!$this->imageManager->imageCanBeDeleted($image)) {
                return;
            }

            $this->imageManager->cacheRemove($image, self::TARGETED_FILTERS);
            $this->imageManager->remove($image);
        }
    }

    /**
     * Removing old cached image and the real one, from both uploads directories.
     */
    public function preRemove(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Image) {
            return;
        }

        $this->imageManager->cacheRemove($entity, self::TARGETED_FILTERS);
        $this->imageManager->remove($entity);
    }
}
