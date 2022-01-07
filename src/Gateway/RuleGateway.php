<?php

declare(strict_types=1);

namespace App\Gateway;

use App\Entity\Rule;

/**
 * @template T
 */
interface RuleGateway
{
    public function create(Rule $rule): void;
}
