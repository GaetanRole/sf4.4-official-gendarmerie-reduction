<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use \DateTime;
use \DateTimeImmutable;
use \DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * A trait for createdAt and updatedAt properties in every entities.
 *
 * I am not using https://packagist.org/packages/gedmo/doctrine-extensions because of TimeContinuum dependency.
 * Private instead of Protected because of a well know behaviour from Doctrine --regenerate.
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
trait EntityTimeTrait
{
    /**
     * @var DateTimeImmutable
     *
     * @ORM\Column(type="datetime_immutable")
     */
    private $createdAt;

    /**
     * @var DateTimeInterface|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt
            = $createdAt instanceof DateTime ? DateTimeImmutable::createFromMutable($createdAt) : $createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
