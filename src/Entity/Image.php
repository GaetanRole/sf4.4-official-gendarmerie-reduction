<?php

declare(strict_types = 1);

namespace App\Entity;

use \InvalidArgumentException;
use App\Entity\Traits\EntityIdTrait;
use App\Entity\Traits\EntityTimeTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity()
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

    /** @var bool */
    private $deleted = false;

    public function getId(): ?int
    {
        return $this->id;
    }

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

    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $isDeleted): void
    {
        $this->deleted = $isDeleted;
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
