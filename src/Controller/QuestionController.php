<?php

namespace App\Controller;

use App\Entity\Question;
use App\Repository\QuestionRepository;
use App\Service\MarkdownHelper;
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
        $question = new Question();
        $question->setName('Missing pants')
            ->setSlug('missing-pants-' . rand(0, 100))
            ->setQuestion(<<<EOF
Hi! So... I'm having a *weird* day. Yesterday, I cast a spell
to make my dishes wash themselves. But while I was casting it,
I slipped a little and I think `I also hit my pants with the spell`.
When I woke up this morning, I caught a quick glimpse of my pants
opening the front door and walking out! I've been out all afternoon
(with no pants mind you) searching for them.
Does anyone have a spell to call your pants back?
EOF
            );

        $question->setVotes(rand(-20, 50));

        if (rand(1, 10) > 2) {
            $question->setAskedAt(new \DateTimeImmutable(sprintf('-%d days', rand(1, 100))));
        }

        $this->entityManager->persist($question);
        $this->entityManager->flush();

        return new Response(sprintf(
            'Well hello! The shiny new question is id #%d, slug %s',
            $question->getId(),
            $question->getSlug()
        ));
    }

    /**
     * @Route("/questions/{slug}", name="questions.show")
     */
    public function show(Question $question): Response
    {
        if ($this->isDebug) {
            $this->logger->info('We are in debug mode');
        }

        $answers = [
            'Make sure your cat is sitting `purrrfectly` still',
            'Honestly, I like furry shoes better than MY cat',
            'Maybe... try saying the spell backwards?',
        ];

        return $this->render('question/show.html.twig', [
            'question' => $question,
            'answers' => $answers,
        ]);
    }

    /**
     * @Route("/questions/{slug}/vote", name="questions.vote", methods="POST")
     */
    public function questionVote(Question $question, Request $request)
    {
        if ($request->get('direction') === 'up') {
            $question->upVote();
        } elseif ($request->get('direction') === 'down') {
            $question->downVote();
        }

        $this->entityManager->flush();

        return $this->redirectToRoute('questions.show', ['slug' => $question->getSlug()]);
    }
}
