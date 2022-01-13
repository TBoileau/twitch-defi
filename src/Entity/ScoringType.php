<?php

declare(strict_types=1);

namespace App\Entity;

enum ScoringType: string
{
    case Bonus = 'bonus';
    case Penalty = 'penalty';

    public function label(): string
    {
        return match($this) {
            self::Bonus => 'Bonus',
            self::Penalty => 'Pénalité',
        };
    }
}
