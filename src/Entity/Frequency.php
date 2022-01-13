<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Embeddable;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

#[Embeddable]
class Frequency
{
    #[Column(type: Types::INTEGER)]
    #[NotBlank]
    #[GreaterThan(0)]
    private int $value;

    #[Column(type: Types::STRING)]
    #[NotBlank]
    private string $unity;

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    public function getUnity(): string
    {
        return $this->unity;
    }

    public function setUnity(string $unity): void
    {
        $this->unity = $unity;
    }
}
