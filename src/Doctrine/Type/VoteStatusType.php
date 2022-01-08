<?php

declare(strict_types=1);

namespace App\Doctrine\Type;

use App\Entity\VoteStatus;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\StringType;

final class VoteStatusType extends StringType
{
    public const NAME = 'vote_status';

    public function getName(): string
    {
        return self::NAME;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): ?string
    {
        if (!$value instanceof VoteStatus) {
            return null;
        }

        return $value->value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): ?VoteStatus
    {
        if (!is_string($value)) {
            return null;
        }

        return VoteStatus::from($value);
    }
}
