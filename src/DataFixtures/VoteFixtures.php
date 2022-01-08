<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Ballot;
use App\Entity\Rule;
use App\Entity\RuleState;
use App\Entity\User;
use App\Entity\Vote;
use App\Entity\VoteStatus;
use App\Repository\RuleRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
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
            ->setParameter('states', [
                RuleState::UnderVote->value,
                RuleState::Accepted->value,
                RuleState::Rejected->value,
            ])
            ->getQuery()
            ->getResult();

        /** @var UserRepository $userRepository */
        $userRepository = $manager->getRepository(User::class);

        /** @var array<array-key, User> $users */
        $users = $userRepository->findAll();

        foreach ($rules as $rule) {
            $ballot = $this->createBallot($rule, $users);
            $manager->persist($ballot);

            foreach ($ballot->getVotes() as $vote) {
                $manager->persist($vote);
            }
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
    private function createBallot(Rule $rule, array $users): Ballot
    {
        $ballot = new Ballot();
        $ballot->setRule($rule);

        $rule->setCurrentBallot($ballot);

        if (RuleState::UnderVote !== $rule->getState()) {
            $ballot->setFinishedAt(new DateTimeImmutable());
        }

        $votersByRuleStateAndVoteStatus = [
            RuleState::Accepted->value => [
                VoteStatus::ACCEPT->value => array_slice($users, 0, 6),
                VoteStatus::REJECT->value => array_slice($users, 6, 2),
                VoteStatus::REVIEW->value => array_slice($users, 8, 2),
            ],
            RuleState::Rejected->value => [
                VoteStatus::ACCEPT->value => array_slice($users, 0, 2),
                VoteStatus::REJECT->value => array_slice($users, 2, 6),
                VoteStatus::REVIEW->value => array_slice($users, 8, 2),
            ],
            RuleState::UnderVote->value => [
                VoteStatus::ACCEPT->value => array_slice($users, 0, 2),
                VoteStatus::REJECT->value => array_slice($users, 2, 2),
                VoteStatus::REVIEW->value => array_slice($users, 4, 2),
            ],
        ];

        foreach ($votersByRuleStateAndVoteStatus[$rule->getState()->value] as $status => $voters) {
            foreach ($voters as $voter) {
                $ballot->getVotes()->add($this->createVote($ballot, $voter, VoteStatus::from($status)));
            }
        }

        return $ballot;
    }

    private function createVote(Ballot $ballot, User $voter, VoteStatus $status): Vote
    {
        $vote = new Vote();
        $vote->setBallot($ballot);
        $vote->setVoter($voter);
        $vote->setStatus($status);

        return $vote;
    }
}
