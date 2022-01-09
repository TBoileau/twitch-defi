<?php

declare(strict_types=1);

namespace App\Tests\Integration\Entity;

use App\Entity\Rule;
use App\Entity\User;
use App\Tests\Integration\ValidationRule;
use App\Tests\Integration\ValidationTestCase;
use Generator;
use Symfony\Component\Validator\Constraints\NotBlank;

final class RuleTest extends ValidationTestCase
{
    /**
     * @return Generator<string, array<array-key, ValidationRule>>
     */
    public function provideEntities(): Generator
    {
        yield 'valid rule' => [
            self::createValidationRule(
                $this->createRule(),
                groups: []
            ),
        ];

        yield 'empty name' => [
            self::createValidationRule()
                ->setData($this->createRule(name: ''))
                ->addError('name', NotBlank::class),
        ];

        yield 'empty description' => [
            self::createValidationRule()
                ->setData($this->createRule(description: ''))
                ->addError('description', NotBlank::class),
        ];
    }

    private function createRule(string $name = 'Règle', string $description = 'Description'): Rule
    {
        $rule = new Rule();
        $rule->setAuthor(new User());
        $rule->setName($name);
        $rule->setDescription($description);

        return $rule;
    }
}
