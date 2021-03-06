<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixtures extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $userPasswordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 1; $i <= 10; ++$i) {
            $manager->persist($this->createUser($i));
        }

        $manager->flush();
    }

    private function createUser(int $index): User
    {
        $user = new User();
        $user->setEmail(sprintf('user+%d@email.com', $index));
        $user->setNickname(sprintf('user+%d', $index));
        $user->setPassword($this->userPasswordHasher->hashPassword($user, 'password'));

        return $user;
    }
}
