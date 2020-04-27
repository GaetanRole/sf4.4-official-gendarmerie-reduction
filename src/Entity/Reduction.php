<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\EntityIdTrait;
use App\Entity\Traits\EntityTimeTrait;
use App\Entity\Traits\EntityUserIdentityTrait;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use App\Validator\Constraints as AppAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReductionRepository")
 * @UniqueEntity(fields={"title"})
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Reduction implements EntityInterface
{
    use EntityIdTrait;
    use EntityTimeTrait;
    use EntityUserIdentityTrait;

    /**
     * Use constants to define configuration options that rarely change instead
     * of specifying them under parameters section in config/services.yaml file.
     *
     * See https://symfony.com/doc/current/best_practices/configuration.html#constants-vs-configuration-options
     */
    public const NUM_ITEMS = 10;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reductions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var Brand
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Brand", inversedBy="reductions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $brand;

    /**
     * @var Category[]|ArrayCollection
     *
     * @ORM\ManyToMany(
     *     targetEntity="App\Entity\Category",
     *     inversedBy="reductions",
     *     cascade={"persist"}
     * )
     * @ORM\OrderBy({"name": "ASC"})
     * @Assert\Count(
     *     min = "1",
     *     max = "3",
     *     minMessage = "validator.reduction.categories.min_count",
     *     maxMessage = "validator.reduction.categories.max_count"
     * )
     */
    private $categories;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, unique=true)
     * @Assert\NotBlank(message="validator.reduction.title.not_blank")
     * @Assert\Length(
     *     min=5,
     *     minMessage="validator.reduction.title.min_length",
     *     max=64,
     *     maxMessage="validator.reduction.title.max_length"
     * )
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(type="string", unique=true, length=100)
     */
    private $slug;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="validator.reduction.description.not_blank")
     * @Assert\Length(
     *     min=10,
     *     minMessage="validator.reduction.description.min_length",
     *     max=10000,
     *     maxMessage="validator.reduction.description.max_length"
     * )
     */
    private $description;

    /**
     * @var Image|null
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Image", cascade={"all"}, orphanRemoval=true)
     *
     * @AppAssert\ImageObject(
     *     mimeTypes={"image/jpeg", "image/png"},
     *     maxSize="1M",
     *     maxWidth="960",
     *     maxHeight="720"
     * )
     */
    private $image;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=13)
     * @Assert\NotBlank(message="validator.reduction.region.not_blank")
     * @Assert\Regex(
     *     pattern="/^[0-9]{2}$|(\bInternational\b|\bNational\b)/",
     *     message="validator.reduction.region.regex"
     * )
     */
    private $region;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=3, nullable=true)
     * @Assert\Regex(
     *     pattern="/^[0-9A-Za-z]{2,3}$/",
     *     message="validator.reduction.department.regex"
     * )
     */
    private $department;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     * @Assert\Length(
     *     min=1,
     *     minMessage="validator.reduction.municipality.min_length",
     *     max=64,
     *     maxMessage="validator.reduction.municipality.max_length"
     * )
     * @Assert\Regex(
     *     pattern="/\d/",
     *     match=false,
     *     message="validator.reduction.municipality.regex"
     * )
     */
    private $municipality;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isBigDeal;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\OneToMany(
     *     targetEntity="App\Entity\Opinion",
     *     mappedBy="reduction",
     *     orphanRemoval=true,
     *     cascade={"persist"}
     * )
     * @ORM\OrderBy({"createdAt": "DESC"})
     */
    private $opinions;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->opinions = new ArrayCollection();
        $this->isBigDeal = false;
        $this->isActive = false;
    }

    /* Auto generated methods */

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(Brand $brand): self
    {
        $this->brand = $brand;
        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category ...$categories): self
    {
        foreach ($categories as $category) {
            if (!$this->categories->contains($category)) {
                $this->categories->add($category);
            }
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->categories->contains($category)) {
            $this->categories->removeElement($category);
        }

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function setRegion(string $region): self
    {
        $this->region = $region;
        return $this;
    }

    public function getDepartment(): ?string
    {
        return $this->department;
    }

    public function setDepartment(?string $department): self
    {
        $this->department = $department;
        return $this;
    }

    public function getMunicipality(): ?string
    {
        return $this->municipality;
    }

    public function setMunicipality(?string $municipality): self
    {
        $this->municipality = $municipality;
        return $this;
    }

    public function getIsBigDeal(): ?bool
    {
        return $this->isBigDeal;
    }

    public function setIsBigDeal(bool $isBigDeal): self
    {
        $this->isBigDeal = $isBigDeal;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|Opinion[]
     */
    public function getOpinions(): Collection
    {
        return $this->opinions;
    }

    public function addOpinion(Opinion $opinion): self
    {
        if (!$this->opinions->contains($opinion)) {
            $this->opinions[] = $opinion;
            $opinion->setReduction($this);
        }

        return $this;
    }

    public function removeOpinion(Opinion $opinion): self
    {
        if ($this->opinions->contains($opinion)) {
            $this->opinions->removeElement($opinion);
            if ($this === $opinion->getReduction()) {
                $opinion->setReduction(null);
            }
        }

        return $this;
    }
}
