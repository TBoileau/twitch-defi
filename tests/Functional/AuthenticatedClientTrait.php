<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

trait AuthenticatedClientTrait
{
    public static function createAuthenticatedClient(string $email = 'user+1@email.com'): KernelBrowser
    {
        $client = self::createClient();

        /**
         * @var UserRepository $userRepository
         * @phpstan-ignore-next-line
         */
        $userRepository = $client->getContainer()->get(UserRepository::class);

        /** @var User $user */
        $user = $userRepository->findOneBy(['email' => $email]);

        $client->loginUser($user);

        return $client;
    }
}
