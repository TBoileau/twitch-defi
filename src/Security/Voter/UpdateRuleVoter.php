<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Rule;
use App\Entity\RuleState;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\CacheableVoterInterface;

final class UpdateRuleVoter implements CacheableVoterInterface
{
    public const NAME = 'update';

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

        if (
            null === $user
            || !in_array($rule->getState(), [RuleState::Draft, RuleState::InReview], true)
        ) {
            return self::ACCESS_DENIED;
        }

        return $rule->getAuthor() === $user ? self::ACCESS_GRANTED : self::ACCESS_DENIED;
    }
}
