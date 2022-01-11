<?php

declare(strict_types=1);

namespace App\Entity;

enum ScoringType: string
{
    case BONUS = 'bonus';
    case PENALTY = 'penalty';
}
