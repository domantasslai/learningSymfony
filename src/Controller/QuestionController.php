<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class QuestionController extends AbstractController
{
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
    public function show($slug)
    {
        $answers = [
            'Make sure your cat is sitting perfectly still',
            'Honestly, I like furry shoes better than MY cat',
            'Maybe... try saying the spell backwards?',
        ];

        return $this->render('question/show.html.twig', [
            'question' => ucwords(str_replace('-', ' ', $slug)),
            'answers' => $answers,
        ]);
    }
}
