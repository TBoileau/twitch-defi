<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Entity\ScoringType;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class ScoringTypeType extends StringType
{
    public const NAME = 'scoring_type';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (!$value instanceof ScoringType) {
            return null;
        }

        return $value->value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?ScoringType
    {
        if (!is_string($value)) {
            return null;
        }

        return ScoringType::from($value);
    }
}
