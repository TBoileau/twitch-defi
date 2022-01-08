<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Ballot;
use App\Gateway\BallotGateway;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Ballot>
 *
 * @template-implements BallotGateway<Ballot>
 */
final class BallotRepository extends ServiceEntityRepository implements BallotGateway
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ballot::class);
    }
}
