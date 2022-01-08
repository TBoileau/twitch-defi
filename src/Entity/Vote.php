<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\VoteRepository;
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

#[Entity(repositoryClass: VoteRepository::class)]
class Vote
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Rule::class, inversedBy: 'votes')]
    #[JoinColumn(nullable: false)]
    private Rule $rule;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $createdAt;

    /**
     * @var Collection<int, User>
     */
    #[ManyToMany(targetEntity: User::class)]
    #[JoinTable(name: 'rule_up_votes')]
    private Collection $upVotes;

    /**
     * @var Collection<int, User>
     */
    #[ManyToMany(targetEntity: User::class)]
    #[JoinTable(name: 'rule_down_votes')]
    private Collection $downVotes;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->upVotes = new ArrayCollection();
        $this->downVotes = new ArrayCollection();
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

    /**
     * @return Collection<int, User>
     */
    public function getUpVotes(): Collection
    {
        return $this->upVotes;
    }

    /**
     * @return Collection<int, User>
     */
    public function getDownVotes(): Collection
    {
        return $this->downVotes;
    }
}
