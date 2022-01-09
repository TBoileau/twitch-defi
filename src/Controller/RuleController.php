<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Rule;
use App\Entity\User;
use App\Form\RuleType;
use App\Gateway\RuleGateway;
use App\Security\Voter\UpdateRuleVoter;
use DateTimeImmutable;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rules', name: 'rule_')]
#[IsGranted('ROLE_USER')]
final class RuleController extends AbstractController
{
    #[Route('/show', name: 'show')]
    public function show(): Response
    {
        return $this->render('rule/show.html.twig');
    }

    /**
     * @param RuleGateway<User> $ruleGateway
     */
    #[Route('/submit', name: 'submit')]
    public function submit(Request $request, RuleGateway $ruleGateway): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw new HttpException(Response::HTTP_UNAUTHORIZED);
        }

        $rule = new Rule();
        $rule->setAuthor($user);

        $form = $this->createForm(RuleType::class, $rule)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ruleGateway->submit($rule);

            return $this->redirectToRoute('home');
        }

        return $this->renderForm('rule/submit.html.twig', ['form' => $form]);
    }

    /**
     * @param RuleGateway<User> $ruleGateway
     */
    #[Route('/{id}/update', name: 'update', requirements: ['id' => '\d+'])]
    #[IsGranted(UpdateRuleVoter::NAME, subject: 'rule')]
    public function update(Rule $rule, Request $request, RuleGateway $ruleGateway): Response
    {
        $form = $this->createForm(RuleType::class, $rule)->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $rule->setUpdatedAt(new DateTimeImmutable());
            $ruleGateway->update($rule);

            return $this->redirectToRoute('home');
        }

        return $this->renderForm('rule/update.html.twig', ['form' => $form]);
    }
}
