<?php

declare(strict_types=1);

namespace App\Gateway;

use App\Entity\Rule;

/**
 * @template T
 */
interface RuleGateway
{
    public function submit(Rule $rule): void;

    public function update(Rule $rule): void;
}
