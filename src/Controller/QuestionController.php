<?php

namespace App\Controller;

use App\Entity\Question;
use App\Enum\AnswerStatus;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Service\MarkdownHelper;
use App\Service\VotingService;
use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
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
     * @Route("/{page<\d+>}", name="questions.index")
     */
    public function homepage(Environment $twigEnvironment, QuestionRepository $questionRepository, int $page = 1)
    {
        /*
        $html = $twigEnvironment->render('question/homepage.html.twig');

        return new Response(($html));
        */
        $queryBuilder = $questionRepository->createAskedOrderedByNewestQueryBuilder();

        $pagerfanta = new Pagerfanta(
            new QueryAdapter($queryBuilder)
        );

        $pagerfanta->setMaxPerPage(5);
        $pagerfanta->setCurrentPage($page);

        // Same as:
        return $this->render('question/homepage.html.twig', [
            'pager' => $pagerfanta
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
//        $answers = $question->getAnswers();
//        dd($question->getApprovedAnswers());
        return $this->render('question/show.html.twig', [
            'question' => $question,
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
