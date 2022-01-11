<?php

declare(strict_types=1);

namespace App\Tests\Functional\Rule;

use App\Entity\Frequency;
use App\Entity\Rule;
use App\Entity\RuleState;
use App\Entity\Scoring;
use App\Entity\ScoringType;
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

        $crawler = $client->request(Request::METHOD_GET, '/rules/submit');

        self::assertResponseIsSuccessful();

        /**
         * @var array{
         *      name: string,
         *      description: string,
         *      scorings: array<array-key, array{
         *          type: string,
         *          label: string,
         *          points: int,
         *          frequency: array{
         *              value: int,
         *              unity: string
         *          },
         *      }>
         * } $formData
         */
        $formData = $this->createData();

        /** @phpstan-ignore-next-line */
        $csrfToken = $crawler->selectButton('Soumettre')->form()->get('rule[_token]')->getValue();

        $client->request(Request::METHOD_POST, '/rules/submit', [
            'rule' => [
                    '_token' => $csrfToken,
                ] + $formData,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $client->followRedirect();

        self::assertRouteSame('home');

        /** @var RuleRepository $ruleRepository */
        $ruleRepository = $client->getContainer()->get(RuleRepository::class);

        self::assertEquals(51, $ruleRepository->count([]));

        /** @var Rule $rule */
        $rule = $ruleRepository->findOneBy([], ['id' => 'desc']);

        self::assertEquals($formData['name'], $rule->getName());
        self::assertEquals($formData['description'], $rule->getDescription());
        self::assertEquals(RuleState::Draft, $rule->getState());
        self::assertEquals('user+1@email.com', $rule->getAuthor()->getEmail());
        self::assertNull($rule->getCurrentBallot());
        self::assertNull($rule->getDecisiveBallot());
        self::assertCount(0, $rule->getBallots());
        self::assertCount(1, $rule->getScorings());
        /** @var Scoring $scoring */
        $scoring = $rule->getScorings()->first();
        self::assertEquals($formData['scorings'][0]['label'], $scoring->getLabel());
        self::assertEquals($formData['scorings'][0]['type'], $scoring->getType()->value);
        self::assertEquals($formData['scorings'][0]['points'], $scoring->getPoints());
        /** @var Frequency $frequency */
        $frequency = $scoring->getFrequency();
        self::assertEquals($formData['scorings'][0]['frequency']['value'], $frequency->getValue());
        self::assertEquals($formData['scorings'][0]['frequency']['unity'], $frequency->getUnity());
    }

    /**
     * @param array{
     *      name: string,
     *      description: string,
     *      scorings: array<array-key, array{
     *          type: string,
     *          label: string,
     *          points: int,
     *          frequency: array{
     *              value: int,
     *              unity: string
     *          },
     *      }>
     * } $formData
     *
     * @test
     *
     * @dataProvider provideInvalidData
     */
    public function shouldNotSubmitRuleDueToInvalidData(array $formData): void
    {
        $client = self::createAuthenticatedClient();

        $crawler = $client->request(Request::METHOD_GET, '/rules/submit');

        self::assertResponseIsSuccessful();

        /** @phpstan-ignore-next-line */
        $csrfToken = $crawler->selectButton('Soumettre')->form()->get('rule[_token]')->getValue();

        $client->request(Request::METHOD_POST, '/rules/submit', [
            'rule' => [
                '_token' => $csrfToken,
            ] + $formData,
        ]);

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @return Generator<string,array<array-key,mixed>>
     */
    public function provideInvalidData(): Generator
    {
        yield 'empty name' => [$this->createData(['name' => ''])];
        yield 'empty description' => [$this->createData(['description' => ''])];
        yield 'empty scoring label' => [$this->createData(['scorings' => ['label' => '']])];
        yield 'empty frequency unity' => [$this->createData(['scorings' => ['frequency' => ['unity' => '']]])];
        yield 'frequency unity less than or equal 0' => [$this->createData([
            'scorings' => ['frequency' => ['value' => 0]],
        ])];
        yield 'scoring points less than or equal 0' => [$this->createData(['scorings' => ['points' => 0]])];
        yield 'no scoring' => [$this->createData(['scorings' => []])];
    }

    /**
     * @param array<string, mixed> $extra
     *
     * @return array<string, mixed>
     */
    private function createData(array $extra = []): array
    {
        return $extra + [
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
    }
}
