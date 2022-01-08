<?php

declare(strict_types=1);

namespace App\Tests\Unit\Form;

use App\Entity\User;
use App\Form\RegistrationType;
use Symfony\Component\Form\Test\TypeTestCase;

final class RegistrationTypeTest extends TypeTestCase
{
    /**
     * @test
     */
    public function shouldSubmitValidData(): void
    {
        $formData = [
            'email' => 'user+11@email.com',
            'plainPassword' => 'password',
            'nickname' => 'user+11',
        ];

        $user = new User();

        $form = $this->factory->create(RegistrationType::class, $user);

        $expectedUser = new User();
        $expectedUser->setNickname('user+11');
        $expectedUser->setEmail('user+11@email.com');
        $expectedUser->setPlainPassword('password');

        $formView = $form->createView();
        self::assertArrayHasKey('nickname', $formView->children);
        self::assertArrayHasKey('email', $formView->children);
        self::assertArrayHasKey('plainPassword', $formView->children);

        $form->submit($formData);
        self::assertTrue($form->isSynchronized());
        self::assertTrue($form->isValid());
        self::assertEquals($expectedUser, $user);
    }
}
