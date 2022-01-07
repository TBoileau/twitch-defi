<?php

declare(strict_types=1);

namespace App\Entity;

enum RuleState: string
{
    case Draft = 'draft';

    case InReview = 'in_review';

    case UnderVote = 'under_vote';

    case Rejected = 'rejected';

    case Accepted = 'accepted';
}
