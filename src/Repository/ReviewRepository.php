<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Review;
use App\Gateway\ReviewGateway;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Review>
 *
 * @template-implements ReviewGateway<Review>
 */
final class ReviewRepository extends ServiceEntityRepository implements ReviewGateway
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }
}
