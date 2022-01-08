<?php

declare(strict_types=1);

namespace App\Gateway;

use App\Entity\User;

/**
 * @template T
 */
interface UserGateway
{
    public function create(User $user): void;
}
