<?php

declare(strict_types = 1);

namespace App\EventListener;

use App\Entity\Image;
use App\Entity\Reduction;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ImageStorageListener
{
    /** @var string */
    private const TARGETED_FILTERS = 'thumbnail';

    /** @var CacheManager */
    private $cacheManager;

    /** @var Filesystem */
    private $fileSystem;

    /** @var string */
    private $uploadDirectory;

    public function __construct(
        CacheManager $cacheManager,
        Filesystem $fileSystem,
        string $imageUploadDirectory
    ) {
        $this->cacheManager = $cacheManager;
        $this->fileSystem = $fileSystem;
        $this->uploadDirectory = $imageUploadDirectory;
    }

    /**
     * Removing old cached image from liip_imagine.
     */
    public function postUpdate(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();

        if (!$entity instanceof Reduction || !$entity->getImage()) {
            return;
        }

        $file = $entity->getImage()->getFilePath('uploads/images');

        $this->cacheManager->resolve($file, self::TARGETED_FILTERS);
        $this->cacheManager->remove($file, self::TARGETED_FILTERS);
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

        $file = $entity->getFilePath('uploads/images');

        $this->cacheManager->resolve($file, self::TARGETED_FILTERS);
        $this->cacheManager->remove($file, self::TARGETED_FILTERS);

        $this->fileSystem->remove($entity->getFilePath($this->uploadDirectory));

        $entity->setFile(null);
        $entity->setDeleted(true);
    }
}
