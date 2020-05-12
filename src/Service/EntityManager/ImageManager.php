<?php

declare(strict_types=1);

namespace App\Service\EntityManager;

use \Exception;
use App\Entity\Image;
use App\Service\GlobalClock;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
final class ImageManager
{
    /** @var CacheManager */
    private $cacheManager;

    /** @var Filesystem */
    private $fileSystem;

    /** @var GlobalClock */
    private $clock;

    /** @var string */
    private $imageUploadDirectory;

    public function __construct(
        CacheManager $cacheManager,
        Filesystem $fileSystem,
        GlobalClock $globalClock,
        string $imageUploadDirectory
    ) {
        $this->cacheManager = $cacheManager;
        $this->fileSystem = $fileSystem;
        $this->clock = $globalClock;
        $this->imageUploadDirectory = $imageUploadDirectory;
    }

    /**
     * Prepare the Image entity to receive a new file.
     *
     * @throws Exception From DateTime provided by GlobalClock and Uuid::uuid4
     */
    public function prepare(Image $image): Image
    {
        if ($image->getCreatedAt()) {
            $image->setUpdatedAt($this->clock->getNowInDateTime());

            return $image;
        }

        $image->setUuid(Uuid::uuid4());
        $image->setCreatedAt($this->clock->getNowInDateTime());

        return $image;
    }

    public function upload(UploadedFile $file, Image $image): File
    {
        $image->syncWithUploadedFile();

        return $file->move($this->getTargetDirectory(), $image->getFilePath());
    }

    /**
     * Image is not removed if this one is null, not uploaded or not linked to a file.
     * You could use $reduction->setImageOutOfContext() to return true.
     */
    public function imageCanBeDeleted(?Image $image): bool
    {
        return !(!$image || !$image->isUploaded() || !$image->getFile());
    }

    public function remove(Image $image): void
    {
        $file = $image->getFilePath($this->imageUploadDirectory);
        if (file_exists($file)) {
            $this->fileSystem->remove($file);
        }
    }

    public function cacheRemove(Image $image, string $filter): void
    {
        $file = $image->getFilePath('uploads/images');

        $this->cacheManager->resolve($file, $filter);
        if ($this->cacheManager->isStored($file, $filter)) {
            $this->cacheManager->remove($file, $filter);
        }
    }

    public function getTargetDirectory(): string
    {
        return $this->imageUploadDirectory;
    }
}
