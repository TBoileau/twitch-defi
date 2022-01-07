<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Rule;
use App\Gateway\RuleGateway;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @template-extends ServiceEntityRepository<Rule>
 *
 * @template-implements RuleGateway<Rule>
 */
final class RuleRepository extends ServiceEntityRepository implements RuleGateway
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Rule::class);
    }

    public function create(Rule $rule): void
    {
        $this->_em->persist($rule);
        $this->_em->flush($rule);
    }
}
