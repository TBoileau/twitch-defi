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

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (!$value instanceof RuleState) {
            return null;
        }

        return $value->value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?RuleState
    {
        if (!is_string($value)) {
            return null;
        }

        return RuleState::from($value);
    }
}
