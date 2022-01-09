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

final class SubmitRuleTest extends WebTestCase
{
    use AuthenticatedClientTrait;

    /**
     * @test
     */
    public function shouldRedirectToLogin(): void
    {
        $client = self::createClient();

        $client->request(Request::METHOD_GET, '/rules/submit');

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('security_login');
    }

    /**
     * @test
     */
    public function shouldSubmitRule(): void
    {
        $client = self::createAuthenticatedClient();

        $client->request(Request::METHOD_GET, '/rules/submit');

        self::assertResponseIsSuccessful();

        $formData = $this->createData();

        $client->submitForm('Soumettre', $formData);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('home');

        /**
         * @var RuleRepository $ruleRepository
         * @phpstan-ignore-next-line
         */
        $ruleRepository = $client->getContainer()->get(RuleRepository::class);

        self::assertEquals(51, $ruleRepository->count([]));

        /** @var Rule $rule */
        $rule = $ruleRepository->findOneBy([], ['id' => 'desc']);

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
    public function shouldNotSubmitRuleDueToInvalidData(array $formData): void
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
            'rule[name]' => 'name',
            'rule[description]' => 'description',
        ];
    }
}
