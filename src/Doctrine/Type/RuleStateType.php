<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Entity\RuleState;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class RuleStateType extends StringType
{
    public const NAME = 'rule_state';

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * @param RuleState|null $value
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        return $value->value;
    }

    /**
     * @param string|null $value
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): ?RuleState
    {
        if (null === $value) {
            return null;
        }

        return RuleState::from($value);
    }
}
