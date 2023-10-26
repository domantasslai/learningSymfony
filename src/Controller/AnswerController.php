<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use App\Service\VotingService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AnswerController extends AbstractController
{
    #[Route('/answers/{id}/vote', name: 'answer_vote', methods: "POST")]
    #[IsGranted('IS_AUTHENTICATED_REMEMBERED')]
    public function answerVote(Request $request, Answer $answer, VotingService $votingService, LoggerInterface $logger): Response
    {
//        $logger->info("{user} is voting on answer {answer}", ['user' => $this->getUser()->getUserIdentifier(), 'answer' => $answer->getId()]);
//        dd($this->getUser());

        $data = json_decode($request->getContent(), true);

        $votingService->vote($answer, $data['direction']);

        return $this->json(['votes' => $answer->getVotes()]);
    }

    #[Route(path: "/answers/popular", name: "app_popular_answers", methods: "GET")]
    public function popularAnswers(AnswerRepository $answerRepository, Request $request)
    {
        $answers = $answerRepository->findMostPopular($request->query->get('q'));

        return $this->render('answer/popular_answer.html.twig', ['answers' => $answers]);
    }
}
