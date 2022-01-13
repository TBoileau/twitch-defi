<?php

declare(strict_types=1);

namespace App\Entity;

enum VoteStatus: string
{
    case Review = 'review';
    case Accept = 'accept';
    case Reject = 'reject';
}
