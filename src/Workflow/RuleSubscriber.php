<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Rule;
use App\Entity\RuleState;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\GuardEvent;

final class RuleSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.rule.guard.reject' => ['guardVote'],
            'workflow.rule.guard.accept' => ['guardVote'],
            'workflow.rule.guard.review' => ['guardVote'],
        ];
    }

    public function guardVote(GuardEvent $event): void
    {
        if (!$event->getMarking()->has(RuleState::UnderVote->value)) {
            return;
        }

        // TODO Vérifier si le nombre minimum de votes est respecté
    }
}
