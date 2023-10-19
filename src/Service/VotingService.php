<?php

namespace App\Service;

use App\Controller\QuestionController;
use App\Entity\Question;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

class VotingService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function vote(Question $question, string $direction): Question
    {
        if ($direction === 'up') {
            $question->upVote();
        } elseif ($direction === 'down') {
            $question->downVote();
        }

        $this->entityManager->flush();

        return $question;
    }
}
