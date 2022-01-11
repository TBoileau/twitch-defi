<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Frequency;
use App\Entity\Rule;
use App\Entity\Scoring;
use App\Entity\ScoringType;
use App\Form\RuleType;
use DateTimeImmutable;
use ReflectionProperty;
use Symfony\Component\Form\Test\TypeTestCase;

final class RuleTypeTest extends TypeTestCase
{
    /**
     * @test
     */
    public function shouldSubmitValidData(): void
    {
        $formData = [
            'name' => 'Règle',
            'description' => 'Description',
            'scorings' => [
                [
                    'type' => ScoringType::BONUS->value,
                    'label' => 'Scoring',
                    'points' => 10,
                    'frequency' => [
                        'value' => 10,
                        'unity' => 'minute',
                    ],
                ],
            ],
        ];

        $rule = new Rule();

        $form = $this->factory->create(RuleType::class, $rule);

        $expectedRule = new Rule();
        $expectedRule->setDescription('Description');
        $expectedRule->setName('Règle');

        $scoring = new Scoring();
        $scoring->setPoints(10);
        $scoring->setLabel('Scoring');
        $scoring->setType(ScoringType::BONUS);

        $frequency = new Frequency();
        $frequency->setValue(10);
        $frequency->setUnity('minute');

        $scoring->setFrequency($frequency);

        $expectedRule->addScoring($scoring);

        $formView = $form->createView();
        self::assertArrayHasKey('name', $formView->children);
        self::assertArrayHasKey('description', $formView->children);
        self::assertArrayHasKey('scorings', $formView->children);
        self::assertArrayHasKey('prototype', $formView->children['scorings']->vars);

        $form->submit($formData);
        self::assertTrue($form->isSynchronized());
        self::assertTrue($form->isValid());

        $unifyDates = static function (Rule $rule, string $property, DateTimeImmutable $date): void {
            $reflectionProperty = new ReflectionProperty($rule, $property);
            $reflectionProperty->setAccessible(true);
            $reflectionProperty->setValue($rule, $date);
        };

        $date = new DateTimeImmutable();

        $unifyDates($rule, 'createdAt', $date);
        $unifyDates($rule, 'updatedAt', $date);
        $unifyDates($expectedRule, 'createdAt', $date);
        $unifyDates($expectedRule, 'updatedAt', $date);

        self::assertEquals($expectedRule, $rule);
    }
}
