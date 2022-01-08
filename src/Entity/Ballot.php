<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\BallotRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;

#[Entity(repositoryClass: BallotRepository::class)]
class Ballot
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Rule::class, inversedBy: 'ballots')]
    #[JoinColumn(nullable: false)]
    private Rule $rule;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $expiredAt;

    #[Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeImmutable $finishedAt = null;

    /**
     * @var Collection<int, Vote>
     */
    #[OneToMany(mappedBy: 'ballot', targetEntity: Vote::class)]
    private Collection $votes;

    public function __construct()
    {
        $this->votes = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->expiredAt = new DateTimeImmutable('8 days midnight');
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRule(): Rule
    {
        return $this->rule;
    }

    public function setRule(Rule $rule): void
    {
        $this->rule = $rule;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getExpiredAt(): DateTimeImmutable
    {
        return $this->expiredAt;
    }

    public function getFinishedAt(): ?DateTimeImmutable
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?DateTimeImmutable $finishedAt): void
    {
        $this->finishedAt = $finishedAt;
    }

    /**
     * @return Collection<int, Vote>
     */
    public function getVotes(): Collection
    {
        return $this->votes;
    }

    public function hasVote(?User $voter): bool
    {
        $criteria = Criteria::create();

        $criteria->where(Criteria::expr()->eq('voter', $voter));

        return 1 === $this->votes->matching($criteria)->count();
    }
}
