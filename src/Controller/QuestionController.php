<?php

namespace App\Controller;

use App\Service\MarkdownHelper;
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

    public function __construct(private LoggerInterface $logger, private bool $isDebug)
    {

    }

    /**
     * @Route("/", name="questions.index")
     */
    public function homepage(Request $request, Environment $twigEnvironment)
    {
        /*
        $html = $twigEnvironment->render('question/homepage.html.twig');

        return new Response(($html));
        */

        // Same as:
        return $this->render('question/homepage.html.twig');
    }

    /**
     * @Route("/questions/{slug}", name="questions.show")
     */
    public function show($slug, MarkdownHelper $markdownHelper): Response
    {
        if ($this->isDebug) {
            $this->logger->info('We are in debug mode');
        }

        $answers = [
            'Make sure your cat is sitting `purrrfectly` still',
            'Honestly, I like furry shoes better than MY cat',
            'Maybe... try saying the spell backwards?',
        ];

        $questionText = "I've been turned into a cat, any *thoughts* on how to turn back? While I'm **adorable**, I don't really care for cat food.";

        $parsedQuestionText = $markdownHelper->parse($questionText);

        return $this->render('question/show.html.twig', [
            'question' => ucwords(str_replace('-', ' ', $slug)),
            'questionText' => $parsedQuestionText,
            'answers' => $answers,
        ]);
    }
}
