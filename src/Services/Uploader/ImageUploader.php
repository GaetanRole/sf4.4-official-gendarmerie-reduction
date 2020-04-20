<?php

declare(strict_types = 1);

namespace App\Services\Uploader;

use App\Entity\Image;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class ImageUploader
{
    /** @var string */
    private $targetDirectory;

    public function __construct(string $imageUploadDirectory)
    {
        $this->targetDirectory = $imageUploadDirectory;
    }

    public function upload(UploadedFile $file, Image $image): File
    {
        $image->syncWithUploadedFile();
        return $file->move($this->getTargetDirectory(), $image->getFilePath());
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
