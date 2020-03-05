<?php

declare(strict_types = 1);

namespace App\Entity;

use App\Entity\Traits\EntityIdTrait;
use App\Entity\Traits\EntityTimeTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BrandRepository")
 * @UniqueEntity(fields={"name"})
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Brand implements EntityInterface
{
    use EntityIdTrait;
    use EntityTimeTrait;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, unique=true)
     * @Assert\NotBlank(message="validator.brand.name.not_blank")
     * @Assert\Length(
     *     min=2,
     *     minMessage="validator.brand.name.min_length",
     *     max=64,
     *     maxMessage="validator.brand.name.max_length"
     * )
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="text", nullable=true)
     * @Assert\Length(
     *     max=255,
     *     maxMessage="validator.brand.description.max_length"
     * )
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity="Reduction", mappedBy="brand")
     */
    private $reductions;

    public function __construct()
    {
        $this->reductions = new ArrayCollection();
    }

    /* Personal methods */

    public function __toString(): string
    {
        return $this->name;
    }

    /* Auto generated methods */

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
            $reduction->setBrand($this);
        }

        return $this;
    }

    public function removeReduction(Reduction $reduction): self
    {
        if ($this->reductions->contains($reduction)) {
            $this->reductions->removeElement($reduction);
            if ($this === $reduction->getBrand()) {
                $reduction->setBrand(null);
            }
        }

        return $this;
    }
}
