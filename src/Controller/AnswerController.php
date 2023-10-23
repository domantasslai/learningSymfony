<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Repository\AnswerRepository;
use App\Service\VotingService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AnswerController extends AbstractController
{
    #[Route('/answers/{id}/vote', name: 'answer_vote', methods: "POST")]
    public function answerVote(Request $request, Answer $answer, VotingService $votingService): Response
    {
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
