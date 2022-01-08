<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\Type\VoteStatusType;
use App\Repository\VoteRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;

#[Entity(repositoryClass: VoteRepository::class)]
class Vote
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Ballot::class, inversedBy: 'votes')]
    #[JoinColumn(nullable: false)]
    private Ballot $ballot;

    #[ManyToOne(targetEntity: User::class)]
    #[JoinColumn(nullable: false)]
    private User $voter;

    #[Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $votedAt;

    #[Column(type: VoteStatusType::NAME)]
    private VoteStatus $status;

    public function __construct()
    {
        $this->votedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBallot(): Ballot
    {
        return $this->ballot;
    }

    public function setBallot(Ballot $ballot): void
    {
        $this->ballot = $ballot;
    }

    public function getVoter(): User
    {
        return $this->voter;
    }

    public function setVoter(User $voter): void
    {
        $this->voter = $voter;
    }

    public function getVotedAt(): DateTimeImmutable
    {
        return $this->votedAt;
    }

    public function getStatus(): VoteStatus
    {
        return $this->status;
    }

    public function setStatus(VoteStatus $status): void
    {
        $this->status = $status;
    }
}
