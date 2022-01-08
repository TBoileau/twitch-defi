<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Rule;
use App\Entity\RuleState;
use App\Entity\User;
use App\Entity\Vote;
use App\Repository\RuleRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class VoteFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var RuleRepository $ruleRepository */
        $ruleRepository = $manager->getRepository(Rule::class);

        /** @var array<array-key, Rule> $rules */
        $rules = $ruleRepository->createQueryBuilder('r')
            ->where('r.state IN (:states)')
            ->setParameter('states', [RuleState::Accepted->value, RuleState::Rejected->value])
            ->getQuery()
            ->getResult();

        /** @var UserRepository $userRepository */
        $userRepository = $manager->getRepository(User::class);

        /** @var array<array-key, User> $users */
        $users = $userRepository->findAll();

        foreach ($rules as $rule) {
            $manager->persist($this->createVote($rule, $users));
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [RuleFixtures::class];
    }

    /**
     * @param array<array-key, User> $users
     */
    private function createVote(Rule $rule, array $users): Vote
    {
        $vote = new Vote();
        $vote->setRule($rule);

        if (RuleState::Accepted === $rule->getState()) {
            foreach ($users as $userUpVote) {
                $vote->getUpVotes()->add($userUpVote);
            }
        }

        if (RuleState::Rejected === $rule->getState()) {
            foreach ($users as $userDownVote) {
                $vote->getDownVotes()->add($userDownVote);
            }
        }

        return $vote;
    }
}
