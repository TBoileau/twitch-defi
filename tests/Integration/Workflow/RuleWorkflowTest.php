<?php

declare(strict_types=1);

namespace App\Tests\Integration\Workflow;

use App\Entity\Ballot;
use App\Entity\Rule;
use App\Entity\RuleState;
use App\Entity\Vote;
use App\Entity\VoteStatus;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Workflow\WorkflowInterface;

final class RuleWorkflowTest extends KernelTestCase
{
    /**
     * @test
     */
    public function shouldSaveFinalBallotWhenVoteIsFinished(): void
    {
        self::bootKernel();

        /** @var WorkflowInterface $workflow */
        $workflow = self::getContainer()->get('state_machine.rule');

        $rule = self::createRule(0, 10, 0);

        $workflow->apply($rule, 'accept');

        self::assertNotNull($rule->getDecisiveBallot());
    }

    /**
     * @test
     */
    public function shouldCloseBallotWhenVoteIsFinished(): void
    {
        self::bootKernel();

        /** @var WorkflowInterface $workflow */
        $workflow = self::getContainer()->get('state_machine.rule');

        $rule = self::createRule(10, 0, 0);

        $workflow->apply($rule, 'accept');

        self::assertNotNull($rule->getDecisiveBallot());
        self::assertNotNull($rule->getDecisiveBallot()->getFinishedAt());
    }

    /**
     * @test
     */
    public function shouldSetCurrentBallotAtNullWhenEnterInReview(): void
    {
        self::bootKernel();

        /** @var WorkflowInterface $workflow */
        $workflow = self::getContainer()->get('state_machine.rule');

        $rule = new Rule();
        $rule->setState(RuleState::UnderVote);

        $workflow->apply($rule, 'review');

        self::assertNull($rule->getCurrentBallot());
    }

    /**
     * @test
     */
    public function shouldCreateBallotWhenRulePassToUnderVote(): void
    {
        self::bootKernel();

        /** @var WorkflowInterface $workflow */
        $workflow = self::getContainer()->get('state_machine.rule');

        $rule = new Rule();
        $rule->setState(RuleState::InReview);

        $workflow->apply($rule, 'vote');

        self::assertInstanceOf(Ballot::class, $rule->getCurrentBallot());
    }

    /**
     * @test
     *
     * @dataProvider provideRulesWillBeBlocked
     */
    public function shouldBlockVote(Rule $rule, string $transition): void
    {
        self::bootKernel();

        /** @var WorkflowInterface $workflow */
        $workflow = self::getContainer()->get('state_machine.rule');

        self::assertFalse($workflow->can($rule, $transition));
    }

    /**
     * @return Generator<string, array{rule: Rule, transition: string}>
     */
    public function provideRulesWillBeBlocked(): Generator
    {
        yield 'reject the rule with majority with not enough votes' => [
            'rule' => self::createRule(7, 1, 1),
            'transition' => 'reject',
        ];
        yield 'reject the rule without majority but enough votes' => [
            'rule' => self::createRule(5, 5, 5),
            'transition' => 'reject',
        ];
        yield 'reject the rule without majority but not enough votes' => [
            'rule' => self::createRule(3, 3, 3),
            'transition' => 'reject',
        ];
        yield 'accept the rule with majority with not enough votes' => [
            'rule' => self::createRule(7, 1, 1),
            'transition' => 'accept',
        ];
        yield 'accept the rule without majority but enough votes' => [
            'rule' => self::createRule(5, 5, 5),
            'transition' => 'accept',
        ];
        yield 'accept the rule without majority but not enough votes' => [
            'rule' => self::createRule(3, 3, 3),
            'transition' => 'accept',
        ];
        yield 'go back to review the rule with majority with not enough votes' => [
            'rule' => self::createRule(7, 1, 1),
            'transition' => 'review',
        ];
        yield 'go back to review the rule without majority but enough votes' => [
            'rule' => self::createRule(5, 5, 5),
            'transition' => 'review',
        ];
        yield 'go back to review the rule without majority but not enough votes' => [
            'rule' => self::createRule(3, 3, 3),
            'transition' => 'review',
        ];
    }

    /**
     * @test
     *
     * @dataProvider provideRulesWillBeNotBlocked
     */
    public function shouldNotBlockVote(Rule $rule, string $transition): void
    {
        self::bootKernel();

        /** @var WorkflowInterface $workflow */
        $workflow = self::getContainer()->get('state_machine.rule');

        self::assertTrue($workflow->can($rule, $transition));
    }

    /**
     * @return Generator<string, array{rule: Rule, transition: string}>
     */
    public function provideRulesWillBeNotBlocked(): Generator
    {
        yield 'reject the rule with majority with enough votes' => [
            'rule' => self::createRule(8, 1, 1),
            'transition' => 'reject',
        ];
        yield 'accept the rule with majority with enough votes' => [
            'rule' => self::createRule(1, 8, 1),
            'transition' => 'accept',
        ];
        yield 'go back to review the rule with majority with enough votes' => [
            'rule' => self::createRule(1, 1, 8),
            'transition' => 'review',
        ];
    }

    private static function createRule(int $numberOfRejects, int $numberOfAccepts, int $numberOfReviews): Rule
    {
        $rule = new Rule();
        $rule->setState(RuleState::UnderVote);

        $ballot = new Ballot();

        $ballot->setRule($rule);
        $rule->setCurrentBallot($ballot);

        for ($i = 0; $i < $numberOfRejects; ++$i) {
            $ballot->getVotes()->add(self::createVote(VoteStatus::Reject));
        }

        for ($i = 0; $i < $numberOfAccepts; ++$i) {
            $ballot->getVotes()->add(self::createVote(VoteStatus::Accept));
        }

        for ($i = 0; $i < $numberOfReviews; ++$i) {
            $ballot->getVotes()->add(self::createVote(VoteStatus::Review));
        }

        return $rule;
    }

    private static function createVote(VoteStatus $status): Vote
    {
        $vote = new Vote();
        $vote->setStatus($status);

        return $vote;
    }
}
