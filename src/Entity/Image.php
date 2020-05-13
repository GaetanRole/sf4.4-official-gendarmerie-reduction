<?php

declare(strict_types=1);

namespace App\Entity;

use \InvalidArgumentException;
use App\Entity\Traits\EntityIdTrait;
use App\Entity\Traits\EntityTimeTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Image
{
    use EntityIdTrait;
    use EntityTimeTrait;

    /**
     * @var string
     *
     * @ORM\Column(length=10)
     *
     * @Assert\NotBlank
     * @Assert\Length(max=10)
     */
    private $extension = null;

    /**
     * @var File|null
     *
     * @Assert\File(maxSize="1M", binaryFormat=false, mimeTypes={"image/*"})
     */
    private $file;

    public function getExtension(): ?string
    {
        return $this->extension;
    }

    public function setExtension(string $extension): self
    {
        $this->extension = $extension;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function isUploaded(): bool
    {
        return null !== $this->createdAt;
    }

    public function getFilePath(string $fromPath = null): string
    {
        return sprintf(
            $fromPath.'/%s.%s',
            $this->uuid,
            $this->extension
        );
    }

    public function syncWithUploadedFile(): void
    {
        if (!$this->file || !$this->file->isReadable()) {
            throw new InvalidArgumentException('Invalid file.');
        }

        $this->extension = $this->file->guessExtension();
    }
}
