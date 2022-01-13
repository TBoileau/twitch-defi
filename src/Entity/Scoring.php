<?php

declare(strict_types=1);

namespace App\Entity;

use App\Doctrine\Type\ScoringTypeType;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embedded;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Valid;

#[Entity]
class Scoring
{
    #[Id]
    #[GeneratedValue]
    #[Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ManyToOne(targetEntity: Rule::class, inversedBy: 'scorings')]
    #[JoinColumn(nullable: false)]
    private Rule $rule;

    #[Column(type: Types::STRING)]
    #[NotBlank]
    private string $label;

    #[Column(name: 'scoring_type', type: ScoringTypeType::NAME, length: 7)]
    private ScoringType $type;

    #[Column(type: Types::INTEGER)]
    #[NotBlank]
    #[GreaterThan(0)]
    private int $points;

    #[Embedded(class: Frequency::class, columnPrefix: 'frequency_')]
    #[Valid]
    private ?Frequency $frequency = null;

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

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    public function getType(): ScoringType
    {
        return $this->type;
    }

    public function setType(ScoringType $type): void
    {
        $this->type = $type;
    }

    public function getPoints(): int
    {
        return $this->points;
    }

    public function setPoints(int $points): void
    {
        $this->points = $points;
    }

    public function getFrequency(): ?Frequency
    {
        return $this->frequency;
    }

    public function setFrequency(?Frequency $frequency): void
    {
        $this->frequency = $frequency;
    }
}
