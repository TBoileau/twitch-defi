<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\Rule;
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
        ];

        $rule = new Rule();

        $form = $this->factory->create(RuleType::class, $rule);

        $expectedRule = new Rule();
        $expectedRule->setDescription('Description');
        $expectedRule->setName('Règle');

        $formView = $form->createView();
        self::assertArrayHasKey('name', $formView->children);
        self::assertArrayHasKey('description', $formView->children);

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
