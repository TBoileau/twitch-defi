<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Review;
use App\Entity\Rule;
use App\Entity\RuleState;
use App\Entity\User;
use App\Repository\RuleRepository;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class ReviewFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var RuleRepository $ruleRepository */
        $ruleRepository = $manager->getRepository(Rule::class);

        /** @var array<array-key, Rule> $rules */
        $rules = $ruleRepository->findBy(['state' => RuleState::InReview]);

        /** @var UserRepository $userRepository */
        $userRepository = $manager->getRepository(User::class);

        /** @var array<array-key, User> $users */
        $users = $userRepository->findAll();

        foreach ($rules as $rule) {
            /** @var int $i */
            foreach ($users as $i => $user) {
                $manager->persist($this->createReview($i, $rule, $user, $users));
            }
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [RuleFixtures::class];
    }

    /**
     * @param array<array-key, User> $users
     */
    private function createReview(int $i, Rule $rule, User $user, array $users): Review
    {
        $review = new Review();
        $review->setAuthor($user);
        $review->setRule($rule);
        $review->setContent(sprintf('Review %d', $i));
        foreach (array_slice($users, 0, 5) as $userLike) {
            $review->getLikes()->add($userLike);
        }
        foreach (array_slice($users, 5, 5) as $userDislike) {
            $review->getDislikes()->add($userDislike);
        }

        return $review;
    }
}
