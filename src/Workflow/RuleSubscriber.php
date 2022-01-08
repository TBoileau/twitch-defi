<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Ballot;
use App\Entity\Rule;
use App\Entity\RuleState;
use App\Entity\Vote;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Event\GuardEvent;

final class RuleSubscriber implements EventSubscriberInterface
{
    public function __construct(private int $minimumNumberOfVotes, private float $minimumVotingRate)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.rule.guard.reject' => ['guardVote'],
            'workflow.rule.guard.accept' => ['guardVote'],
            'workflow.rule.guard.review' => ['guardVote'],
            'workflow.rule.enter.in_review' => ['onEnterInReview'],
            'workflow.rule.enter.under_vote' => ['onEnterUnderVote'],
            'workflow.rule.leave.under_vote' => ['onLeaveUnderVote'],
            'workflow.rule.completed' => ['onCompleted'],
        ];
    }

    public function onEnterInReview(Event $event): void
    {
        /** @var Rule $rule */
        $rule = $event->getSubject();

        if (null === $rule->getCurrentBallot()) {
            return;
        }

        $rule->setCurrentBallot(null);
    }

    public function onCompleted(Event $event): void
    {
        /** @var Rule $rule */
        $rule = $event->getSubject();

        if (
            null === $event->getTransition()
            || 0 === $rule->getBallots()->count()
            || !in_array($event->getTransition()->getName(), ['accept', 'reject'], true)
        ) {
            return;
        }

        $lastBallot = $rule->getCurrentBallot();

        $rule->setDecisiveBallot($lastBallot);

        $rule->setCurrentBallot(null);
    }

    public function onLeaveUnderVote(Event $event): void
    {
        /** @var Rule $rule */
        $rule = $event->getSubject();

        if (null === $rule->getCurrentBallot()) {
            return;
        }

        $rule->getCurrentBallot()->setFinishedAt(new \DateTimeImmutable());
    }

    public function onEnterUnderVote(Event $event): void
    {
        /** @var Rule $rule */
        $rule = $event->getSubject();

        $ballot = new Ballot();
        $ballot->setRule($rule);

        $rule->setCurrentBallot($ballot);
    }

    public function guardVote(GuardEvent $event): void
    {
        /** @var Rule $rule */
        $rule = $event->getSubject();

        if (
            !$event->getMarking()->has(RuleState::UnderVote->value)
            || null === $rule->getCurrentBallot()
        ) {
            return;
        }

        $ballot = $rule->getCurrentBallot();

        if ($ballot->getVotes()->count() < $this->minimumNumberOfVotes) {
            $event->setBlocked(true);

            return;
        }

        /** @var array<array-key, int> $numberOfVotesByStatus */
        $numberOfVotesByStatus = array_values(
            array_count_values(
                $ballot
                    ->getVotes()
                    ->map(static fn (Vote $vote) => $vote->getStatus()->value)
                    ->toArray()
            )
        );

        rsort($numberOfVotesByStatus);

        if ($numberOfVotesByStatus[0] / $ballot->getVotes()->count() < $this->minimumVotingRate) {
            $event->setBlocked(true);
        }
    }
}
