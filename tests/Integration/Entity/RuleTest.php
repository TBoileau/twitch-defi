<?php

declare(strict_types=1);

namespace App\Tests\Integration\Entity;

use App\Entity\Frequency;
use App\Entity\Rule;
use App\Entity\Scoring;
use App\Entity\ScoringType;
use App\Entity\User;
use App\Tests\Integration\ValidationRule;
use App\Tests\Integration\ValidationTestCase;
use Generator;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\GreaterThan;
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

        yield 'empty scoring label' => [
            self::createValidationRule()
                ->setData($this->createRule(scoringLabel: ''))
                ->addError('scorings[0].label', NotBlank::class),
        ];

        yield 'empty frequency unity' => [
            self::createValidationRule()
                ->setData($this->createRule(frequencyUnity: ''))
                ->addError('scorings[0].frequency.unity', NotBlank::class),
        ];

        yield 'frequency unity less than or equal 0' => [
            self::createValidationRule()
                ->setData($this->createRule(frequencyValue: 0))
                ->addError('scorings[0].frequency.value', GreaterThan::class),
        ];

        yield 'scoring points less than or equal 0' => [
            self::createValidationRule()
                ->setData($this->createRule(scoringPoints: 0))
                ->addError('scorings[0].points', GreaterThan::class),
        ];

        yield 'no scoring' => [
            self::createValidationRule()
                ->setData($this->createRule(addScoring: false))
                ->addError('scorings', Count::class),
        ];

        yield 'empty description' => [
            self::createValidationRule()
                ->setData($this->createRule(description: ''))
                ->addError('description', NotBlank::class),
        ];
    }

    private function createRule(
        string $name = 'Règle',
        string $description = 'Description',
        bool $addScoring = true,
        string $scoringLabel = 'Scoring',
        int $scoringPoints = 10,
        string $frequencyUnity = 'Unity',
        int $frequencyValue = 10
    ): Rule {
        $rule = new Rule();
        $rule->setAuthor(new User());
        $rule->setName($name);
        $rule->setDescription($description);

        if ($addScoring) {
            $scoring = new Scoring();
            $scoring->setPoints($scoringPoints);
            $scoring->setLabel($scoringLabel);
            $scoring->setType(ScoringType::BONUS);

            $frequency = new Frequency();
            $frequency->setValue($frequencyValue);
            $frequency->setUnity($frequencyUnity);

            $scoring->setFrequency($frequency);

            $rule->addScoring($scoring);
        }

        return $rule;
    }
}
