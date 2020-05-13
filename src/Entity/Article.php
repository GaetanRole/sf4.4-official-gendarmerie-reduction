<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\EntityIdTrait;
use App\Entity\Traits\EntityTimeTrait;
use App\Repository\ArticleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArticleRepository::class)
 * @UniqueEntity(fields={"title"})
 *
 * @author  Gaëtan Rolé-Dubruille <gaetan.role@gmail.com>
 */
class Article implements EntityInterface
{
    use EntityIdTrait;
    use EntityTimeTrait;

    /**
     * Use constants to define configuration options that rarely change instead
     * of specifying them under parameters section in config/services.yaml file.
     *
     * See https://symfony.com/doc/current/best_practices/configuration.html#constants-vs-configuration-options
     */
    public const PRIORITY = [
        'LOW' => 0,
        'MEDIUM' => 1,
        'HIGH' => 2,
        'URGENT' => 3,
    ];

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, unique=true)
     * @Assert\NotBlank(message="validator.article.title.not_blank")
     * @Assert\Length(
     *     min=5,
     *     minMessage="validator.article.title.min_length",
     *     max=64,
     *     maxMessage="validator.article.title.max_length"
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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="validator.article.summary.not_blank")
     * @Assert\Length(
     *     min=5,
     *     minMessage="validator.article.summary.min_length",
     *     max=254,
     *     maxMessage="validator.article.summary.max_length"
     * )
     */
    private $summary;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="validator.article.content.not_blank")
     * @Assert\Length(
     *     min=10,
     *     minMessage="validator.article.content.min_length",
     *     max=1024,
     *     maxMessage="validator.article.content.max_length"
     * )
     */
    private $content;

    /**
     * @var array
     *
     * @ORM\Column(type="array", nullable=true)
     */
    private $resources = [];

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     * @Assert\Range(
     *     min=0,
     *     minMessage="validator.article.priority.min_value",
     *     max=3,
     *     maxMessage="validator.article.priority.max_value"
     * )
     */
    private $priority = self::PRIORITY['MEDIUM'];

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean")
     */
    private $isActive = true;

    public function __toString(): string
    {
        return $this->title;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

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

    public function getSummary(): ?string
    {
        return $this->summary;
    }

    public function setSummary(string $summary): self
    {
        $this->summary = $summary;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getResources(): ?array
    {
        return $this->resources;
    }

    public function setResources(?array $resources): self
    {
        $this->resources = $resources;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;

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
}
