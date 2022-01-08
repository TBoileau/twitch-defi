<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Rule;
use App\Entity\RuleState;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\CacheableVoterInterface;

final class VoteRuleVoter implements CacheableVoterInterface
{
    public const NAME = 'vote';

    public function supportsAttribute(string $attribute): bool
    {
        return self::NAME === $attribute;
    }

    public function supportsType(string $subjectType): bool
    {
        return Rule::class === $subjectType;
    }

    /**
     * @param array<array-key, mixed> $attributes
     */
    public function vote(TokenInterface $token, mixed $subject, array $attributes): int
    {
        /** @var Rule $rule */
        $rule = $subject;

        /** @var ?User $user */
        $user = $token->getUser();

        if (null === $user || RuleState::UnderVote !== $rule->getState() || null === $rule->getCurrentBallot()) {
            return self::ACCESS_DENIED;
        }

        if ($rule->getAuthor() === $user) {
            return self::ACCESS_DENIED;
        }

        return $rule->getCurrentBallot()->hasVote($user) ? self::ACCESS_DENIED : self::ACCESS_GRANTED;
    }
}
