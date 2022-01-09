<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Rule;
use App\Entity\User;
use App\Form\RuleType;
use App\Gateway\RuleGateway;
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
            $ruleGateway->create($rule);

            return $this->redirectToRoute('home');
        }

        return $this->renderForm('rule/submit.html.twig', ['form' => $form]);
    }
}
