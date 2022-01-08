<?php

declare(strict_types=1);

namespace App\Tests\Unit\Voter;

use App\Entity\Ballot;
use App\Entity\Rule;
use App\Entity\RuleState;
use App\Entity\User;
use App\Entity\Vote;
use App\Entity\VoteStatus;
use App\Security\Voter\VoteRuleVoter;
use Generator;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\NullToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

final class VoteRuleVoterTest extends TestCase
{
    /**
     * @test
     *
     * @dataProvider provideDataForVoter
     */
    public function shouldDeniedRuleVote(Rule $rule, ?User $user = null): void
    {
        $voter = new VoteRuleVoter();

        $token = null !== $user ? new UsernamePasswordToken($user, 'main') : new NullToken();

        self::assertEquals(
            VoterInterface::ACCESS_DENIED,
            $voter->vote($token, $rule, [])
        );
    }

    /**
     * @return Generator<string, array{rule: Rule, user: User|null}>
     */
    public function provideDataForVoter(): Generator
    {
        yield 'user is not logged' => [
            'rule' => $this->createRule(new User(), RuleState::UnderVote, true, null),
            'user' => null,
        ];
        yield 'rule is not under vote' => [
            'rule' => $this->createRule(new User(), RuleState::Accepted, false, null),
            'user' => new User(),
        ];
        yield 'rule has not a current ballot' => [
            'rule' => $this->createRule(new User(), RuleState::UnderVote, false, null),
            'user' => new User(),
        ];

        $user = new User();

        yield 'voter is the rule\'s author' => [
            'rule' => $this->createRule($user, RuleState::UnderVote, true, $user),
            'user' => $user,
        ];

        $user = new User();

        yield 'voter has already voted' => [
            'rule' => $this->createRule(new User(), RuleState::UnderVote, true, $user),
            'user' => $user,
        ];
    }

    private function createRule(User $author, RuleState $state, bool $createBallot, ?User $voter): Rule
    {
        $rule = new Rule();
        $rule->setState($state);
        $rule->setAuthor($author);

        if (!$createBallot) {
            return $rule;
        }

        $ballot = new Ballot();

        $ballot->setRule($rule);
        $rule->setCurrentBallot($ballot);

        if (null !== $voter) {
            $vote = new Vote();
            $vote->setStatus(VoteStatus::REJECT);
            $vote->setVoter($voter);
            $ballot->getVotes()->add($vote);
        }

        return $rule;
    }
}
