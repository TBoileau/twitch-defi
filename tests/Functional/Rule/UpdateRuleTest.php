<?php

declare(strict_types=1);

namespace App\Tests\Functional\Rule;

use App\Entity\Rule;
use App\Entity\RuleState;
use App\Repository\RuleRepository;
use App\Tests\Functional\AuthenticatedClientTrait;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class UpdateRuleTest extends WebTestCase
{
    use AuthenticatedClientTrait;

    /**
     * @test
     */
    public function shouldRedirectToLogin(): void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/rules/1/update');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    /**
     * @test
     */
    public function shouldRaiseAnAccessDeniedExceptionWhenLoggedUserIdDifferentOfRulesAuthor(): void
    {
        $client = self::createAuthenticatedClient();

        $client->request(Request::METHOD_GET, '/rules/6/update');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @test
     */
    public function shouldRaiseAnAccessDeniedExceptionWhenRulesAuthorTryToUpdatePublishedRule(): void
    {
        $client = self::createAuthenticatedClient();

        $client->request(Request::METHOD_GET, '/rules/4/update');

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * @test
     */
    public function shouldUpdateRule(): void
    {
        $client = self::createAuthenticatedClient();

        /**
         * @var RuleRepository $ruleRepository
         * @phpstan-ignore-next-line
         */
        $ruleRepository = $client->getContainer()->get(RuleRepository::class);

        /** @var Rule $rule */
        $rule = $ruleRepository->find(1);

        $client->request(Request::METHOD_GET, sprintf('/rules/%d/update', $rule->getId()));

        self::assertResponseIsSuccessful();

        $formData = $this->createData();

        $client->submitForm('Modifier', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('home');

        /**
         * @var RuleRepository $ruleRepository
         * @phpstan-ignore-next-line
         */
        $ruleRepository = $client->getContainer()->get(RuleRepository::class);

        self::assertEquals(50, $ruleRepository->count([]));

        /** @var Rule $rule */
        $rule = $ruleRepository->find(1);

        self::assertEquals($formData['rule[name]'], $rule->getName());
        self::assertEquals($formData['rule[description]'], $rule->getDescription());
        self::assertEquals(RuleState::Draft, $rule->getState());
        self::assertEquals('user+1@email.com', $rule->getAuthor()->getEmail());
        self::assertNull($rule->getCurrentBallot());
        self::assertNull($rule->getDecisiveBallot());
        self::assertCount(0, $rule->getBallots());
    }

    /**
     * @param array<string, string> $formData
     *
     * @test
     *
     * @dataProvider provideInvalidData
     */
    public function shouldNotUpdateRuleDueToInvalidData(array $formData): void
    {
        $client = self::createAuthenticatedClient();

        $client->request(Request::METHOD_GET, '/rules/submit');

        self::assertResponseIsSuccessful();

        $client->submitForm('Soumettre', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return Generator<string, array<array-key, array<string, string>>>
     */
    public function provideInvalidData(): Generator
    {
        yield 'empty name' => [$this->createData(['rule[name]' => ''])];
        yield 'empty description' => [$this->createData(['rule[description]' => ''])];
    }

    /**
     * @param array<string, string> $extra
     *
     * @return array<string, string>
     */
    private function createData(array $extra = []): array
    {
        return $extra + [
            'rule[name]' => 'updated name',
            'rule[description]' => 'updated description',
        ];
    }
}
