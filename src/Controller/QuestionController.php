<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Service\MarkdownHelper;
use App\Service\VotingService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sentry\State\HubAdapter;
use Sentry\State\HubInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class QuestionController extends AbstractController
{

    public function __construct(private LoggerInterface $logger, private bool $isDebug, private EntityManagerInterface $entityManager)
    {

    }

    /**
     * @Route("/", name="questions.index")
     */
    public function homepage(Request $request, Environment $twigEnvironment, QuestionRepository $questionRepository)
    {
        /*
        $html = $twigEnvironment->render('question/homepage.html.twig');

        return new Response(($html));
        */

        $questions = $questionRepository->findAllAskedByNewest();

        // Same as:
        return $this->render('question/homepage.html.twig', [
            'questions' => $questions
        ]);
    }

    /**
     * @Route("/questions/new", name="questions.new")
     */
    public function new(): Response
    {
        return new Response();
    }

    /**
     * @Route("/questions/{slug}", name="questions.show")
     */
    public function show(Question $question, AnswerRepository $answerRepository): Response
    {
        if ($this->isDebug) {
            $this->logger->info('We are in debug mode');
        }

//        $answers = $answerRepository->findBy([
//            'question' => $question
//        ]);
        // OR
        $answers = $question->getAnswers();
//        dd($answers);

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answers,
        ]);
    }

    /**
     * @Route("/questions/{slug}/vote", name="questions.vote", methods="POST")
     */
    public function questionVote(Question $question, Request $request, VotingService $votingService)
    {
        $votingService->vote($question, $request->get('direction'));

        return $this->redirectToRoute('questions.show', ['slug' => $question->getSlug()]);
    }
}
