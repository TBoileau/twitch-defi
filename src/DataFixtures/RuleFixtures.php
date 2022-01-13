<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Frequency;
use App\Entity\Rule;
use App\Entity\RuleState;
use App\Entity\Scoring;
use App\Entity\ScoringType;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

final class RuleFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        /** @var UserRepository $userRepository */
        $userRepository = $manager->getRepository(User::class);

        /** @var array<array-key, User> $users */
        $users = $userRepository->findAll();

        foreach ($users as $user) {
            for ($i = 0; $i < 5; ++$i) {
                $manager->persist($this->createRule($i, $user));
            }
        }

        $manager->flush();
    }

    /**
     * @return array<array-key, class-string<Fixture>>
     */
    public function getDependencies(): array
    {
        return [UserFixtures::class];
    }

    private function createRule(int $index, User $user): Rule
    {
        $rule = new Rule();
        $rule->setAuthor($user);
        $rule->setName(sprintf('Rule %d', $index));
        $rule->setDescription(sprintf('Description %d', $index));
        $rule->setState(RuleState::cases()[$index]);

        $scoring = new Scoring();
        $scoring->setPoints(10);
        $scoring->setLabel('Scoring');
        $scoring->setType(ScoringType::Bonus);

        $frequency = new Frequency();
        $frequency->setValue(10);
        $frequency->setUnity('minute');

        $scoring->setFrequency($frequency);

        $rule->addScoring($scoring);

        return $rule;
    }
}
