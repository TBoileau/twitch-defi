<?php

declare(strict_types=1);

namespace App\Entity;

enum VoteStatus: string
{
    case REVIEW = 'review';
    case ACCEPT = 'accept';
    case REJECT = 'reject';
}
