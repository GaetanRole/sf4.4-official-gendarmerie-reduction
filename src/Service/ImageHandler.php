<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\Image;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class ImageHandler
{
    /** @var CacheManager */
    private $cacheManager;

    /** @var Filesystem */
    private $fileSystem;

    /** @var string */
    private $imageUploadDirectory;

    public function __construct(
        CacheManager $cacheManager,
        Filesystem $fileSystem,
        string $imageUploadDirectory
    ) {
        $this->cacheManager = $cacheManager;
        $this->fileSystem = $fileSystem;
        $this->imageUploadDirectory = $imageUploadDirectory;
    }

    public function upload(UploadedFile $file, Image $image): File
    {
        $image->syncWithUploadedFile();
        return $file->move($this->getTargetDirectory(), $image->getFilePath());
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

    public function imageCanBeDeleted(?Image $image): bool
    {
        return !(!$image || !$image->isUploaded() || !$image->getFile());
    }

    public function getTargetDirectory(): string
    {
        return $this->imageUploadDirectory;
    }
}
