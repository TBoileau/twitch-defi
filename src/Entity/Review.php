<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ReviewRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity(repositoryClass: ReviewRepository::class)]
class Review
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: false)]
    private User $author;

    #[ManyToOne(targetEntity: Rule::class)]
    #[JoinColumn(nullable: false)]
    private Rule $rule;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $publishedAt;

    #[Column(type: Types::TEXT)]
    private string $content;

    /**
     * @var Collection<int, User>
     */
    #[ManyToMany(targetEntity: User::class)]
    #[JoinTable(name: 'review_likes')]
    private Collection $likes;

    /**
     * @var Collection<int, User>
     */
    #[ManyToMany(targetEntity: User::class)]
    #[JoinTable(name: 'review_dislikes')]
    private Collection $dislikes;

    public function __construct()
    {
        $this->publishedAt = new DateTimeImmutable();
        $this->likes = new ArrayCollection();
        $this->dislikes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function getRule(): Rule
    {
        return $this->rule;
    }

    public function setRule(Rule $rule): void
    {
        $this->rule = $rule;
    }

    public function getPublishedAt(): DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function setPublishedAt(DateTimeImmutable $publishedAt): void
    {
        $this->publishedAt = $publishedAt;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return Collection<int, User>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    /**
     * @return Collection<int, User>
     */
    public function getDislikes(): Collection
    {
        return $this->dislikes;
    }
}
