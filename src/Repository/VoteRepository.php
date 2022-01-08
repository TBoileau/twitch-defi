<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Vote;
use App\Gateway\VoteGateway;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Vote>
 *
 * @template-implements VoteGateway<Vote>
 */
final class VoteRepository extends ServiceEntityRepository implements VoteGateway
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Vote::class);
    }
}
