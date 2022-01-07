<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use App\Gateway\UserGateway;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<User>
 */
final class UserRepository extends ServiceEntityRepository implements UserGateway
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function create(User $user): void
    {
        $this->_em->persist($user);
        $this->_em->flush($user);
    }
}
