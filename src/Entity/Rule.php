<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\Type\RuleStateType;
use App\Repository\RuleRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Entity(repositoryClass: RuleRepository::class)]
class Rule
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[Column(type: RuleStateType::NAME, length: 10)]
    private RuleState $state = RuleState::Draft;

    #[Column(type: Types::STRING)]
    #[NotBlank]
    private string $name;

    #[Column(type: Types::TEXT)]
    #[NotBlank]
    private string $description;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: false)]
    private User $author;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $updatedAt;

    /**
     * @var Collection<int, Ballot>
     */
    #[OneToMany(mappedBy: 'rule', targetEntity: Ballot::class)]
    private Collection $ballots;

    #[ManyToOne(targetEntity: Ballot::class)]
    private ?Ballot $currentBallot = null;

    #[ManyToOne(targetEntity: Ballot::class)]
    private ?Ballot $decisiveBallot = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = new DateTimeImmutable();
        $this->ballots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getState(): RuleState
    {
        return $this->state;
    }

    public function setState(RuleState $state): void
    {
        $this->state = $state;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $author): void
    {
        $this->author = $author;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeImmutable $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setScalarState(string $state): void
    {
        $this->state = RuleState::from($state);
    }

    public function getScalarState(): string
    {
        return $this->state->value;
    }

    /**
     * @return Collection<int, Ballot>
     */
    public function getBallots(): Collection
    {
        return $this->ballots;
    }

    public function getCurrentBallot(): ?Ballot
    {
        return $this->currentBallot;
    }

    public function setCurrentBallot(?Ballot $currentBallot): void
    {
        $this->currentBallot = $currentBallot;

        if (null !== $this->currentBallot) {
            $this->ballots->add($this->currentBallot);
        }
    }

    public function getDecisiveBallot(): ?Ballot
    {
        return $this->decisiveBallot;
    }

    public function setDecisiveBallot(?Ballot $decisiveBallot): void
    {
        $this->decisiveBallot = $decisiveBallot;
    }
}
