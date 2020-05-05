<?php

declare(strict_types=1);

namespace App\Entity;

use \JsonSerializable;
use App\Entity\Traits\EntityIdTrait;
use App\Entity\Traits\EntityTimeTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CategoryRepository")
 * @UniqueEntity(fields={"name"})
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Category implements JsonSerializable, EntityInterface
{
    use EntityIdTrait;
    use EntityTimeTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, unique=true)
     * @Assert\NotBlank(message="validator.category.name.not_blank")
     * @Assert\Length(
     *     min=2,
     *     minMessage="validator.category.name.min_length",
     *     max=64,
     *     maxMessage="validator.category.name.max_length"
     * )
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *     max=255,
     *     maxMessage="validator.category.description.max_length"
     * )
     */
    private $description;

    /**
     * @ORM\ManyToMany(targetEntity="Reduction", mappedBy="categories")
     */
    private $reductions;

    public function __construct()
    {
        $this->reductions = new ArrayCollection();
    }

    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): string
    {
        // http://php.net/manual/en/class.jsonserializable.php
        // e.g. categories|json_encode
        return $this->name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|Reduction[]
     */
    public function getReductions(): Collection
    {
        return $this->reductions;
    }

    public function addReduction(Reduction $reduction): self
    {
        if (!$this->reductions->contains($reduction)) {
            $this->reductions[] = $reduction;
            $reduction->addCategory($this);
        }

        return $this;
    }

    public function removeReduction(Reduction $reduction): self
    {
        if ($this->reductions->contains($reduction)) {
            $this->reductions->removeElement($reduction);
            $reduction->removeCategory($this);
        }

        return $this;
    }
}
