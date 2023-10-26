<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Service\VotingService;
use Doctrine\ORM\EntityManagerInterface;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Twig\Environment;

// fourth way: (globally on all controller routes)
//#[IsGranted('ROLE_ADMIN')]
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

    #[Route('/questions/new', name:"questions.new")]
    // Third way:
    #[IsGranted("ROLE_USER")]
    public function new(): Response
    {
        // Adding access to role
        // first way:
//        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        // second way:
//        if (!$this->isGranted('ROLE_ADMIN')) {
//            throw $this->createAccessDeniedException('No access for you!');
//        }

        return new Response('Sounds like a GREAT feature for V2!');
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
